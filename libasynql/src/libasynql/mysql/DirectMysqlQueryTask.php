<?php

/*
 * libasynql
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

namespace libasynql\mysql;

class DirectMysqlQueryTask extends MysqlQueryTask{
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

		$taskResult = MysqlUtils::query($mysqli, $this->query, $args);

		$this->setResult($taskResult);
	}
}
