<?php

/*
 * libasynql
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
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use poggit\libasynql\SqlThread;

class SqlThreadPool implements SqlThread{
	/** @var SleeperNotifier */
	private $notifier;
	/** @var callable */
	private $workerFactory;
	/** @var SqlSlaveThread[] */
	private $workers = [];
	/** @var int */
	private $workerLimit;

	/** @var QuerySendQueue */
	private $bufferSend;
	/** @var QueryRecvQueue */
	private $bufferRecv;

	/** @var DataConnectorImpl|null */
	private $dataConnector = null;

	/**
	 * @param DataConnectorImpl $dataConnector
	 */
	public function setDataConnector(DataConnectorImpl $dataConnector): void {
		$this->dataConnector = $dataConnector;
	}

	/**
	 * SqlThreadPool constructor.
	 *
	 * @param callable $workerFactory create a child worker: <code>function(?Threaded $bufferSend = null, ?Threaded $bufferRecv = null) : {@link BaseSqlThread}{}</code>
	 * @param int      $workerLimit   the maximum number of workers to create. Workers are created lazily.
	 */
	public function __construct(callable $workerFactory, int $workerLimit){
		$this->notifier = new SleeperNotifier();
		Server::getInstance()->getTickSleeper()->addNotifier($this->notifier, function() : void{
			assert($this->dataConnector instanceof DataConnectorImpl); // otherwise, wtf
			$this->dataConnector->checkResults();
		});

		$this->workerFactory = $workerFactory;
		$this->workerLimit = $workerLimit;
		$this->bufferSend = new QuerySendQueue();
		$this->bufferRecv = new QueryRecvQueue();

		$this->addWorker();
	}

	private function addWorker() : void{
		$this->workers[] = ($this->workerFactory)($this->notifier, $this->bufferSend, $this->bufferRecv);
	}

	public function join() : void{
		foreach($this->workers as $worker){
			$worker->join();
		}
	}

	public function stopRunning() : void{
		foreach($this->workers as $worker){
			$worker->stopRunning();
		}
	}

	public function addQuery(int $queryId, array $modes, array $queries, array $params) : void{
		$this->bufferSend->scheduleQuery($queryId, $modes, $queries, $params);

		// check if we need to increase worker size
		foreach($this->workers as $worker){
			if(!$worker->isBusy()){
				return;
			}
		}
		if(count($this->workers) < $this->workerLimit){
			$this->addWorker();
		}
	}

	public function readResults(array &$callbacks) : void{
		while($this->bufferRecv->fetchResults($queryId, $results)){
			if(!isset($callbacks[$queryId])){
				throw new InvalidArgumentException("Missing handler for query #$queryId");
			}

			$callbacks[$queryId]($results);
			unset($callbacks[$queryId]);
		}
	}

	public function connCreated() : bool{
		return $this->workers[0]->connCreated();
	}

	public function hasConnError() : bool{
		return $this->workers[0]->hasConnError();
	}

	public function getConnError() : ?string{
		return $this->workers[0]->getConnError();
	}

	public function getLoad() : float{
		return $this->bufferSend->count() / (float) $this->workerLimit;
	}
}
