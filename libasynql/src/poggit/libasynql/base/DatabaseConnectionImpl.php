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
use poggit\libasynql\DatabaseConnection;
use poggit\libasynql\generic\GenericStatementFileParser;
use poggit\libasynql\GenericStatement;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlThread;

class DatabaseConnectionImpl implements DatabaseConnection{
	/** @var SqlThread */
	private $thread;
	/** @var GenericStatement[] */
	private $queries = [];
	private $handlers = [];
	private $queryId = 0;
	/** @var string|null */
	private $placeHolder;

	/**
	 * Creates a DatabaseConnection.
	 *
	 * @param SqlThread   $thread      the backend SqlThread to connect with
	 * @param null|string $placeHolder the backend-implementation-dependent placeholder. <code>"?"</code> for mysqli-based backends, <code>null</code> for PDO-based and SQLite3-based backends.
	 */
	public function __construct(SqlThread $thread, ?string $placeHolder){
		$this->thread = $thread;
		$this->placeHolder = $placeHolder;
	}

	public function loadQueryFile($fh) : void{
		$parser = new GenericStatementFileParser($fh);
		$parser->parse();
		foreach($parser->getResults() as $result){
			$this->loadQuery($result);
		}
	}

	public function loadQuery(GenericStatement $stmt) : void{
		if(!isset($this->queries[$stmt->getName()])){
			throw new InvalidArgumentException("Duplicate GenericStatement: {$stmt->getName()}");
		}
		$this->queries[$stmt->getName()] = $stmt;
	}

	public function executeGeneric(string $name, array $args, ?callable $onSuccess = null, ?callable $onError = null) : void{
		$queryId = $this->queryId++;
		$this->handlers[$queryId] = function($result) use ($onSuccess, $onError){
			if($result instanceof SqlError){
				$onError($result);
			}else{
				$onSuccess($result);
			}
		};

		$query = $this->queries[$name]->format($args, $this->placeHolder, $outArgs);
		$this->thread->addQuery($queryId, SqlThread::MODE_GENERIC, $query, $outArgs);
	}

	public function executeChange(string $name, array $args, ?callable $onSuccess = null, ?callable $onError = null) : void{
		$queryId = $this->queryId++;
		$this->handlers[$queryId] = function($result) use ($onSuccess, $onError){
			if($result instanceof SqlError){
				$onError($result);
			}else{
				$onSuccess($result);
			}
		};

		$query = $this->queries[$name]->format($args, $this->placeHolder, $outArgs);
		$this->thread->addQuery($queryId, SqlThread::MODE_CHANGE, $query, $outArgs);
	}

	public function executeInsert(string $name, array $args, ?callable $onInserted = null, ?callable $onError = null) : void{
		$queryId = $this->queryId++;
		$this->handlers[$queryId] = function($result) use ($onInserted, $onError){
			if($result instanceof SqlError){
				$onError($result);
			}else{
				$onInserted($result);
			}
		};

		$query = $this->queries[$name]->format($args, $this->placeHolder, $outArgs);
		$this->thread->addQuery($queryId, SqlThread::MODE_INSERT, $query, $outArgs);
	}

	public function executeSelect(string $name, array $args, ?callable $onSelect = null, ?callable $onError = null) : void{
		$queryId = $this->queryId++;
		$this->handlers[$queryId] = function($result) use ($onSelect, $onError){
			if($result instanceof SqlError){
				$onError($result);
			}else{
				$onSelect($result);
			}
		};

		$query = $this->queries[$name]->format($args, $this->placeHolder, $outArgs);
		$this->thread->addQuery($queryId, SqlThread::MODE_SELECT, $query, $outArgs);
	}

	public function close() : void{
		$this->thread->stopRunning();
	}
}
