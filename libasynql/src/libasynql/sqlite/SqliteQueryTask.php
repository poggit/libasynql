<?php

/*
 *
 * libasynql
 *
 * Copyright (C) 2017 SOFe
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
*/

namespace libasynql\sqlite;

use libasynql\exception\SqlException;
use libasynql\result\SqlErrorResult;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

abstract class SqliteQueryTask extends AsyncTask{
	private $file;
	protected $hasCallback = false;

	public function __construct(string $file, $callback = null){
		$this->file = $file;
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
		}elseif($result instanceof SqlErrorResult){
			$server->getLogger()->logException($result->getException());
		}
	}

	public final function onRun(){
		try{
			$this->execute();
		}catch(SqlException $ex){
			$this->setResult(new SqlErrorResult($ex));
		}
	}

	protected abstract function execute();

	protected function getSqlite() : \SQLite3{
		$identifier = SqliteQueryTask::getIdentifier($this->file);

		$sqlite = $this->getFromThreadStore($identifier);
		if(!($sqlite instanceof \SQLite3)){
			$sqlite = new \SQLite3($this->file);
			$this->saveToThreadStore($identifier, $sqlite);
		}

		return $sqlite;
	}

	public function getFile() : string{
		return $this->file;
	}

	public static function getIdentifier(string $file) : string{
		return str_replace("\\", ".", __NAMESPACE__) . ".sqlite.pool." . basename($file) . "." . substr(md5($file), 0, 8);
	}
}
