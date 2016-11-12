<?php

/*
 * Poggit
 *
 * Copyright (C) 2016 Poggit
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

namespace poggit\virions\libasynql\task;

use poggit\virions\libasynql\MysqlCredentials;
use poggit\virions\libasynql\task\exception\MysqlQueryException;
use poggit\virions\libasynql\task\result\MysqlSuccessResult;

class DirectQueryMysqlTask extends QueryMysqlTask{
	/** @var string */
	private $query;
	/** @var string|array[] */
	private $args;

	/**
	 * Creates an AsyncTask for MySQL queries
	 *
	 * @param MysqlCredentials $credentials Login credentials for MySQL. Always pass a valid value, because a new instance may be created anytime.
	 * @param string           $query       query to execute, optionally with ? to be used with $args
	 * @param array            $args        array of array(type, value)s, e.g. [["i", 1], ["s", "string"]], default empty array
	 * @param callable|null    $callback    Query to execute after task is completed. Accepts one MysqlResult argument. Pass null to execute nothing.
	 */
	public function __construct(MysqlCredentials $credentials, string $query, array $args = [], callable $callback = null){
		parent::__construct($credentials, $callback);
		$this->query = $query;
		$this->args = serialize($args);
	}

	protected function execute(){
		$mysqli = $this->getMysqli();
		$args = unserialize($this->args);

		$taskResult = new MysqlSuccessResult();

		$stmt = $mysqli->prepare($this->query);
		$types = "";
		$params = [];
		foreach($args as list($type, $arg)){
			assert(strlen($type) === 1);
			$types .= $type;
			$params[] = $arg;
		}
		$stmt->bind_param($types, ...$params);
		if(!$stmt->execute()){
			$stmt->close();
			throw new MysqlQueryException($stmt->error);
		}
		$taskResult->affectedRows = $stmt->affected_rows;
		$result = $stmt->get_result();
		if($result instanceof \mysqli_result){
			$taskResult = $taskResult->asSelectResult();
			$taskResult->rows = [];
			while(is_array($row = $result->fetch_assoc())){
				$taskResult->rows[] = $row;
			}
		}
		$stmt->close();
		$result->close();
		$this->setResult($taskResult);
	}
}
