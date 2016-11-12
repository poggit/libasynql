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

namespace poggit\virions\libasynql;

use poggit\virions\libasynql\pool\exception\MysqlConnectException;

class MysqlCredentials implements \JsonSerializable{
	/** @var string */
	private $host;
	/** @var string */
	private $username;
	/** @var string */
	private $password;
	/** @var string */
	private $schema;
	/** @var int */
	private $port;
	/** @var string */
	private $socket;

	/**
	 * Creates a new {@link MysqlCredentials} instance from an array (e.g. from Config)
	 *
	 * @param array $array
	 *
	 * @return MysqlCredentials
	 */
	public static function fromArray(array $array) : MysqlCredentials{
		return new MysqlCredentials($array["host"] ?? "127.0.0.1", $array["username"] ?? "root",
			$array["password"] ?? "", $array["schema"], $array["port"] ?? 3306, $array["socket"] ?? "");
	}

	/**
	 * Constructs a new {@link MysqlCredentials} by passing parameters directly.
	 *
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $schema
	 * @param int    $port
	 * @param string $socket
	 */
	public function __construct(string $host, string $username, string $password, string $schema, int $port = 3306, string $socket = ""){
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->schema = $schema;
		$this->port = $port;
		$this->socket = $socket;
	}

	/**
	 * Creates a new {@link mysqli} instance
	 *
	 * @return \mysqli
	 *
	 * @throws MysqlConnectException
	 */
	public function newMysqli() : \mysqli{
		$mysqli = @new \mysqli($this->host, $this->username, $this->password, $this->schema, $this->port, $this->socket);
		if($mysqli->connect_error){
			throw new MysqlConnectException("Failure to connect to MySQL: $mysqli->connect_error");
		}
		return $mysqli;
	}

	/**
	 * Produces a human-readable output without leaking password
	 *
	 * @return string
	 */
	public function __toString() : string{
		return "$this->username@$this->host:$this->port/schema,$this->socket";
	}

	/**
	 * Prepares value to be var_dump()'ed without leaking password
	 *
	 * @return array
	 */
	public function __debugInfo(){
		return [
			"host" => $this->host,
			"username" => $this->username,
			"password" => str_repeat("*", strlen($this->password)),
			"schema" => $this->schema,
			"port" => $this->port,
			"socket" => $this->socket
		];
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 *        which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize(){
		return [
			"host" => $this->host,
			"username" => $this->username,
			"password" => $this->password,
			"schema" => $this->schema,
			"port" => $this->port,
			"socket" => $this->socket
		];
	}
}
