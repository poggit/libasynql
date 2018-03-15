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

namespace poggit\libasynql;

use InvalidArgumentException;
use pocketmine\Thread;
use Threaded;
use function is_array;

abstract class BaseSqlThread extends Thread implements SqlThread{
	private $running = true;
	protected $bufferSend;
	protected $bufferRecv;
	protected $connCreated = false;
	protected $connError;

	protected function __construct(){
		$this->bufferSend = new Threaded();
		$this->bufferRecv = new Threaded();
	}

	public function run(){
		$error = $this->createConn();
		$this->connCreated = true;
		$this->connError = $error;

		if($error !== null){
			while($this->running){
				while(is_array($querySet = $this->bufferSend->shift())){
					[$queryId, $mode, $query, $params] = $querySet;
					try{
						$result = $this->executeQuery($mode, $query, $params);
					}catch(SqlError $error){
						$result = $error;
					}
					$this->bufferRecv[] = [$queryId, $result];
				}
			}
			$this->close();
		}
	}

	public function isReallyRunning() : bool{
		return $this->running;
	}

	public function stopRunning() : void{
		$this->running = false;
	}

	public function addQuery(int $queryId, int $mode, string $query, array $params) : void{
		$this->bufferSend[] = [$queryId, $mode, $query, $params];
	}

	public function readResults(array &$callbacks) : void{
		while(is_array($resultSet = $this->bufferRecv->shift())){
			[$queryId, $result] = $resultSet;
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

	protected abstract function createConn() : ?string;

	/**
	 * @param int     $mode
	 * @param string  $query
	 * @param mixed[] $params
	 * @return SqlResult
	 * @throws SqlError
	 */
	protected abstract function executeQuery(int $mode, string $query, array $params) : SqlResult;

	protected abstract function close() : void;
}
