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

namespace libasynql;

use libasynql\exception\MysqlConnectException;
use libasynql\exception\MysqlException;
use libasynql\result\MysqlErrorResult;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

abstract class QueryMysqlTask extends AsyncTask{
	/** @var string serialize(MysqlCredentials) */
	private $credentials;
	/** @var bool */
	private $hasCallback = false;

	/**
	 * Initializes a QueryMysqlTask.
	 *
	 * @param MysqlCredentials    $credentials the credentials used to create a connection
	 * @param callable|mixed|null $callback    the callback to execute, or any arbitrary value that shall be retrieved by fetchLocal()
	 */
	public function __construct(MysqlCredentials $credentials, $callback = null){
		$this->credentials = serialize($credentials);
		if($callback !== null){
			parent::__construct($callback);
			if(is_callable($callback)){
				$this->hasCallback = true;
			}
		}
	}

	public function onCompletion(Server $server){
		$result = $this->getResult();
		if($this->hasCallback){
			$cb = $this->fetchLocal($server);
			$cb($result);
		}elseif($result instanceof MysqlErrorResult){
			$server->getLogger()->logException($result->getException());
		}
	}

	public final function onRun(){
		try{
			$this->execute();
		}catch(MysqlException $ex){
			$this->setResult(new MysqlErrorResult($ex));
		}
	}

	protected abstract function execute();

	/**
	 * Fetches the {@link \mysqli} instance used in this async worker thread.
	 *
	 * @return \mysqli
	 *
	 * @throws MysqlConnectException
	 */
	protected function getMysqli() : \mysqli{
		/** @var MysqlCredentials $credentials */
		$credentials = unserialize($this->credentials);
		$identifier = str_replace("\\", ".", __NAMESPACE__) . ".mysql.pool.$credentials";

		$mysqli = $this->getFromThreadStore($identifier);
		if(!($mysqli instanceof \mysqli)){
			$mysqli = $credentials->newMysqli();
			$this->saveToThreadStore($identifier, $mysqli);
		}

		return $mysqli;
	}

	public function getCredentials() : MysqlCredentials{
		return unserialize($this->credentials);
	}
}
