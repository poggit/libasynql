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
use libasynql\exception\SqlQueryException;
use libasynql\result\SqlErrorResult;
use libasynql\result\SqlResult;
use libasynql\result\SqlSelectResult;
use pocketmine\plugin\Plugin;

class SqliteUtils{
	public static function query(\SQLite3 $sqlite, string $query, array $args) : SqlResult{
		$start = microtime(true);
		try{
			$stmt = $sqlite->prepare($query);
			if($stmt === false){
				throw new SqlQueryException($sqlite->lastErrorMsg());
			}
			foreach($args as $name => $val){
				$arg = is_array($val) ? $val[0] : $val;
				if(is_array($val) and isset($val[1])){
					$type = $val[1];
					switch($type){
						case "i":
							$type = SQLITE3_INTEGER;
							break;
						case "f":
						case "d":
							$type = SQLITE3_FLOAT;
							break;
						case "s":
							$type = SQLITE3_TEXT;
							break;
						case "b":
							$type = SQLITE3_BLOB;
							break;
					}
				}else{
					switch(gettype($arg)){
						case 'double':
							$type = SQLITE3_FLOAT;
							break;
						case 'integer':
							$type = SQLITE3_INTEGER;
							break;
						case 'boolean':
							$type = SQLITE3_INTEGER;
							break;
						case 'NULL':
							$type = SQLITE3_NULL;
							break;
						case 'string':
							$type = SQLITE3_TEXT;
							break;
						default:
							throw new \InvalidArgumentException('Argument is of invalid type ' . gettype($arg));
					}
				}
				$successBind = $stmt->bindValue($name, $arg, $type);
				if(!$successBind){
					throw new SqlQueryException("Cannot bind param: " . $sqlite->lastErrorMsg());
				}
			}
			$result = $stmt->execute();
			if($result === false){
				throw new SqlQueryException("Query error: " . $sqlite->lastErrorMsg());
			}

			$taskResult = new SqlSelectResult();
			$taskResult->affectedRows = $sqlite->changes();
			$taskResult->insertId = $sqlite->lastInsertRowID();
			$taskResult->rows = [];
			while(is_array($row = $result->fetchArray(SQLITE3_ASSOC))){
				$taskResult->rows[] = $row;
			}
			$result->finalize();

			$end = microtime(true);

			return $taskResult->setTiming($end - $start);
		}catch(SqlException $ex){
			$end = microtime(true);
			return (new SqlErrorResult($ex))->setTiming($end - $start);
		}finally{
			if(isset($result) and $result instanceof \SQLite3Result){
				try{
					$result->finalize();
				}catch(\Throwable $e){
				}
			}
			if(isset($stmt) and $stmt instanceof \SQLite3Stmt){
				try{
					$stmt->close();
				}catch(\Throwable $e){
				}
			}
		}
	}

	public static function closeAll(Plugin $plugin, string $file){
		$scheduler = $plugin->getServer()->getScheduler();
		$size = $scheduler->getAsyncTaskPoolSize();
		for($i = 0; $i < $size; $i++){
			$scheduler->scheduleAsyncTaskToWorker(new SqliteCloseTask($file), $i);
		}
	}
}
