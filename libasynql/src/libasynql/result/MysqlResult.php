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

namespace libasynql\result;

use libasynql\exception\MysqlException;
use libasynql\exception\MysqlQueryException;

/**
 * Represents a successful or error result from MySQL.
 */
abstract class MysqlResult{
	/** @var float */
	private $timing;

	public static function executeQuery(\mysqli $mysqli, string $query, array $args) : MysqlResult{
		$start = microtime(true);
		try{
			$stmt = $mysqli->prepare($query);
			$types = "";
			$params = [];
			foreach($args as list($type, $arg)){
				assert(strlen($type) === 1);
				$types .= $type;
				$params[] = $arg;
			}
			$stmt->bind_param($types, ...$params);
			if(!$stmt->execute()){
				throw new MysqlQueryException($stmt->error);
			}

			$taskResult = new MysqlSuccessResult();
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
		}catch(MysqlException $ex){
			$end = microtime(true);
			return (new MysqlErrorResult($ex))->setTiming($end - $start);
		}finally{
			if(isset($stmt)){
				$stmt->close();
			}
			if(isset($result) and $result instanceof \mysqli_result){
				$result->close();
			}
		}
	}

	public function setTiming(float $timing) : MysqlResult{
		$this->timing = $timing;
		return $this;
	}

	public function getTiming() : float{
		return $this->timing;
	}
}
