<?php

/*
 * libasynql_v3
 *
 * Copyright (C) 2018 SOFe
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace poggit\libasynql\base;

use InvalidArgumentException;
use pocketmine\Thread;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlResult;
use poggit\libasynql\SqlThread;

abstract class SqlSlaveThread extends Thread implements SqlThread{
	private $running = true;
	protected $bufferSend;
	protected $bufferRecv;
	protected $connCreated = false;
	protected $connError;
	protected $working = false;

	protected function __construct(QuerySendQueue $bufferSend = null, QueryRecvQueue $bufferRecv = null){
		$this->bufferSend = $bufferSend ?? new QuerySendQueue();
		$this->bufferRecv = $bufferRecv ?? new QueryRecvQueue();

		$this->start();
	}

	public function run(){
		$error = $this->createConn($resource);
		$this->connCreated = true;
		$this->connError = $error;

		if($error === null){
			return;
		}

		while($this->running){
			while($this->bufferSend->fetchQuery($queryId, $mode, $query, $params)){
				$this->working = true;
				try{
					$result = $this->executeQuery($resource, $mode, $query, $params);
					$this->bufferRecv->publishResult($queryId, $result);
				}catch(SqlError $error){
					$this->bufferRecv->publishError($queryId, $error);
				}
			}
			$this->working = false;
		}
		$this->close($resource);
	}

	/**
	 * Returns true if this thread is working, false if waiting for requests
	 *
	 * @return bool
	 */
	public function isWorking() : bool{
		return $this->working;
	}

	public function stopRunning() : void{
		$this->running = false;
	}

	public function addQuery(int $queryId, int $mode, string $query, array $params) : void{
		$this->bufferSend->scheduleQuery($queryId, $mode, $query, $params);
	}

	public function readResults(array &$callbacks) : void{
		while($this->bufferRecv->fetchResult($queryId, $result)){
			if(!isset($callbacks[$queryId])){
				throw new InvalidArgumentException("Missing handler for query #$queryId");
			}

			$callbacks[$queryId]($result);
			unset($callbacks[$queryId]);
		}
	}

	public function connCreated() : bool{
		return $this->connCreated;
	}

	public function hasConnError() : bool{
		return $this->connError !== null;
	}

	public function getConnError() : ?string{
		return $this->connError;
	}

	protected abstract function createConn(&$resource) : ?string;

	/**
	 * @param mixed   &$resource
	 * @param int     $mode
	 * @param string  $query
	 * @param mixed[] $params
	 * @return SqlResult
	 * @throws SqlError
	 */
	protected abstract function executeQuery(&$resource, int $mode, string $query, array $params) : SqlResult;

	protected abstract function close(&$resource) : void;
}
