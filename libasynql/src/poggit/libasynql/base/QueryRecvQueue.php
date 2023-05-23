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

use pmmp\thread\ThreadSafe;
use pmmp\thread\ThreadSafeArray;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlResult;
use function is_string;
use function serialize;
use function unserialize;

class QueryRecvQueue extends ThreadSafe{
	private int $availableThreads = 0;

	private ThreadSafeArray $queue;

	public function __construct(){
		$this->queue = new ThreadSafeArray();
	}

	/**
	 * @param SqlResult[] $results
	 */
	public function publishResult(int $queryId, array $results) : void{
		$this->synchronized(function() use ($queryId, $results) : void{
			$this->queue[] = serialize([$queryId, $results]);
			$this->notify();
		});
	}

	public function publishError(int $queryId, SqlError $error) : void{
		$this->synchronized(function() use ($error, $queryId) : void{
			$this->queue[] = serialize([$queryId, $error]);
			$this->notify();
		});
	}

	public function fetchResults(&$queryId, &$results) : bool{
		$row = $this->queue->shift();
		if(is_string($row)){
			[$queryId, $results] = unserialize($row, ["allowed_classes" => true]);
			return true;
		}
		return false;
	}

	/**
	 * @param SqlError|SqlResult[]|null $results
	 */
	public function waitForResults(?int &$queryId, SqlError|array|null &$results) : bool{
		return $this->synchronized(function() use (&$queryId, &$results) : bool{
			while($this->queue->count() === 0 && $this->availableThreads > 0){
				$this->wait();
			}
			return $this->fetchResults($queryId, $results);
		});
	}

	public function addAvailableThread() : void{
		$this->synchronized(fn() => ++$this->availableThreads);
	}

	public function removeAvailableThread() : void{
		$this->synchronized(function() : void{
			--$this->availableThreads;
			$this->notify();
		});
	}
}
