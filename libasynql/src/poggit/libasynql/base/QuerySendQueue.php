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

use Threaded;
use function serialize;

class QuerySendQueue extends Threaded{
	/** @var bool */
	private $invalidated = false;
	/** @var Threaded */
	private $queries;

	public function __construct(){
		$this->queries = new Threaded();
	}

	public function scheduleQuery(int $queryId, array $modes, array $queries, array $params) : void{
		if($this->invalidated){
			throw new QueueShutdownException("You cannot schedule a query on an invalidated queue.");
		}
		$this->synchronized(function() use ($queryId, $modes, $queries, $params) : void{
			$this->queries[] = serialize([$queryId, $modes, $queries, $params]);
			$this->notifyOne();
		});
	}

	public function fetchQuery() : ?string {
		return $this->synchronized(function(): ?string {
			while($this->queries->count() === 0 && !$this->isInvalidated()){
				$this->wait();
			}
			return $this->queries->shift();
		});
	}

	public function invalidate() : void {
		$this->synchronized(function():void{
			$this->invalidated = true;
			$this->notify();
		});
	}

	/**
	 * @return bool
	 */
	public function isInvalidated(): bool {
		return $this->invalidated;
	}
}
