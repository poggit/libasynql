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

namespace libasynql\mysql;

use libasynql\exception\SqlException;
use libasynql\exception\SqlQueryException;
use libasynql\result\SqlErrorResult;
use libasynql\result\SqlResult;
use libasynql\result\SqlSuccessResult;
use pocketmine\plugin\Plugin;

class MysqlUtils{
	public static function query(\mysqli $mysqli, string $query, array $args) : SqlResult{
		$start = microtime(true);
		try{
			$stmt = $mysqli->prepare($query);
			if($stmt === false){
				throw new SqlQueryException($mysqli->error);
			}
			if(count($args) > 0){
				$types = "";
				$params = [];
				foreach($args as list($type, $arg)){
					assert(strlen($type) === 1);
					$types .= $type;
					$params[] = $arg;
				}
				$successBind = $stmt->bind_param($types, ...$params);
				if(!$successBind){
					throw new SqlQueryException($stmt->error);
				}
			}
			if(!$stmt->execute()){
				throw new SqlQueryException($stmt->error);
			}

			$taskResult = new SqlSuccessResult();
			$taskResult->affectedRows = $stmt->affected_rows;
			$result = $stmt->get_result();
			if($result instanceof \mysqli_result){
				$taskResult = $taskResult->asSelectResult();
				$taskResult->rows = [];
				while(is_array($row = $result->fetch_assoc())){
					$taskResult->rows[] = $row;
				}
			}else{
				$taskResult->insertId = $stmt->insert_id;
			}

			$end = microtime(true);

			return $taskResult->setTiming($end - $start);
		}catch(SqlException $ex){
			$end = microtime(true);
			return (new SqlErrorResult($ex))->setTiming($end - $start);
		}finally{
			if(isset($stmt) and $stmt instanceof \mysqli_stmt){
				$stmt->close();
			}
			if(isset($result) and $result instanceof \mysqli_result){
				$result->close();
			}
		}
	}

	public static function init(Plugin $plugin, MysqlCredentials $credentials){
		$task = new MysqlPingTask($plugin, $credentials);
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask($task, 600);
	}

	public static function closeAll(Plugin $plugin, MysqlCredentials $credentials){
		$scheduler = $plugin->getServer()->getScheduler();
		$size = $scheduler->getAsyncTaskPoolSize();
		for($i = 0; $i < $size; $i++){
			$scheduler->scheduleAsyncTaskToWorker(new MysqlCloseTask($credentials), $i);
		}
	}
}
