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

namespace poggit\libasynql\mysqli;

use AttachableThreadedLogger;
use Closure;
use InvalidArgumentException;
use mysqli;
use mysqli_result;
use mysqli_stmt;
use pocketmine\snooze\SleeperNotifier;
use poggit\libasynql\base\QueryRecvQueue;
use poggit\libasynql\base\QuerySendQueue;
use poggit\libasynql\base\SqlSlaveThread;
use poggit\libasynql\result\SqlChangeResult;
use poggit\libasynql\result\SqlColumnInfo;
use poggit\libasynql\result\SqlInsertResult;
use poggit\libasynql\result\SqlSelectResult;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlResult;
use poggit\libasynql\SqlThread;
use function array_map;
use function assert;
use function bccomp;
use function bcsub;
use function count;
use function gettype;
use function implode;
use function in_array;
use function is_float;
use function is_int;
use function is_string;
use function min;
use function serialize;
use function sleep;
use function strtotime;
use function unserialize;
use const PHP_INT_MAX;

class MysqliThread extends SqlSlaveThread{
	/** @var string */
	private $credentials;
	/** @var AttachableThreadedLogger */
	private $logger;

	public static function createFactory(MysqlCredentials $credentials, AttachableThreadedLogger $logger) : Closure{
		return function(SleeperNotifier $notifier, QuerySendQueue $bufferSend, QueryRecvQueue $bufferRecv) use ($credentials, $logger){
			return new MysqliThread($credentials, $notifier, $logger, $bufferSend, $bufferRecv);
		};
	}

	public function __construct(MysqlCredentials $credentials, SleeperNotifier $notifier, AttachableThreadedLogger $logger, QuerySendQueue $bufferSend = null, QueryRecvQueue $bufferRecv = null){
		$this->credentials = serialize($credentials);
		$this->logger = $logger;

		parent::__construct($notifier, $bufferSend, $bufferRecv);
	}

	protected function createConn(&$mysqli) : ?string{
		/** @var MysqlCredentials $cred */
		$cred = unserialize($this->credentials);
		try{
			$mysqli = $cred->newMysqli();

			return null;
		}catch(SqlError $e){
			return $e->getErrorMessage();
		}
	}

	protected function executeQuery($mysqli, int $mode, string $query, array $params) : SqlResult{
		assert($mysqli instanceof mysqli);
		/** @var MysqlCredentials $cred */
		$cred = unserialize($this->credentials);
		if(!$mysqli->ping()){
			$success = false;
			$attempts = 0;
			do{
				$seconds = min(2 ** $attempts, PHP_INT_MAX);
				$this->logger->warning("Database connection failed! Trying reconnecting in $seconds seconds.");
				sleep($seconds);

				try{
					$cred->reconnectMysqli($mysqli);
					$success = true;
				}catch(SqlError $e){
					$attempts++;
				}
			}while(!$success);
			$this->logger->info("Database connection restored.");
		}

		if(count($params) === 0){
			$result = $mysqli->query($query);
			if($result === false){
				throw new SqlError(SqlError::STAGE_EXECUTE, $mysqli->error, $query, []);
			}
			switch($mode){
				case SqlThread::MODE_GENERIC:
				case SqlThread::MODE_CHANGE:
				case SqlThread::MODE_INSERT:
					if($result instanceof mysqli_result){
						$result->close();
					}
					if($mode === SqlThread::MODE_INSERT){
						return new SqlInsertResult($mysqli->affected_rows, $mysqli->insert_id);
					}
					if($mode === SqlThread::MODE_CHANGE){
						return new SqlChangeResult($mysqli->affected_rows);
					}
					return new SqlResult();

				case SqlThread::MODE_SELECT:
					$ret = $this->toSelectResult($result);
					$result->close();
					return $ret;
			}
		}else{
			$stmt = $mysqli->prepare($query);
			if(!($stmt instanceof mysqli_stmt)){
				throw new SqlError(SqlError::STAGE_PREPARE, $mysqli->error, $query, $params);
			}
			$types = implode(array_map(static function($param) use ($query, $params){
				if(is_string($param)){
					return "s";
				}
				if(is_float($param)){
					return "d";
				}
				if(is_int($param)){
					return "i";
				}
				throw new SqlError(SqlError::STAGE_PREPARE, "Cannot bind value of type " . gettype($param), $query, $params);
			}, $params));
			$stmt->bind_param($types, ...$params);
			if(!$stmt->execute()){
				throw new SqlError(SqlError::STAGE_EXECUTE, $stmt->error, $query, $params);
			}
			switch($mode){
				case SqlThread::MODE_GENERIC:
					$ret = new SqlResult();
					$stmt->close();
					return $ret;

				case SqlThread::MODE_CHANGE:
					$ret = new SqlChangeResult($stmt->affected_rows);
					$stmt->close();
					return $ret;

				case SqlThread::MODE_INSERT:
					$ret = new SqlInsertResult($stmt->affected_rows, $stmt->insert_id);
					$stmt->close();
					return $ret;

				case SqlThread::MODE_SELECT:
					$set = $stmt->get_result();
					$ret = $this->toSelectResult($set);
					$set->close();
					return $ret;
			}
		}

		throw new InvalidArgumentException("Unknown mode $mode");
	}

	private function toSelectResult(mysqli_result $result) : SqlSelectResult{
		$columns = [];
		$columnFunc = [];

		while(($field = $result->fetch_field()) !== false){
			if($field->length === 1){
				if($field->type === MysqlTypes::TINY){
					$type = SqlColumnInfo::TYPE_BOOL;
					$columnFunc[$field->name] = static function($tiny){
						return $tiny > 0;
					};
				}elseif($field->type === MysqlTypes::BIT){
					$type = SqlColumnInfo::TYPE_BOOL;
					$columnFunc[$field->name] = static function($bit){
						return $bit === "\1";
					};
				}
			}
			if($field->type === MysqlTypes::LONGLONG){
				$type = SqlColumnInfo::TYPE_INT;
				$columnFunc[$field->name] = static function($longLong) use ($field){
					if($field->flags & MysqlFlags::UNSIGNED_FLAG){
						if(bccomp(strval($longLong), "9223372036854775807") === 1){
							$longLong = bcsub($longLong, "18446744073709551616");
						}
						return (int) $longLong;
					}

					return (int) $longLong;
				};
			}elseif($field->flags & MysqlFlags::TIMESTAMP_FLAG){
				$type = SqlColumnInfo::TYPE_TIMESTAMP;
				$columnFunc[$field->name] = static function($stamp){
					return strtotime($stamp);
				};
			}elseif($field->type === MysqlTypes::NULL){
				$type = SqlColumnInfo::TYPE_NULL;
			}elseif(in_array($field->type, [
				MysqlTypes::VARCHAR,
				MysqlTypes::STRING,
				MysqlTypes::VAR_STRING,
			], true)){
				$type = SqlColumnInfo::TYPE_STRING;
			}elseif(in_array($field->type, [MysqlTypes::FLOAT, MysqlTypes::DOUBLE, MysqlTypes::DECIMAL, MysqlTypes::NEWDECIMAL], true)){
				$type = SqlColumnInfo::TYPE_FLOAT;
				$columnFunc[$field->name] = "floatval";
			}elseif(in_array($field->type, [MysqlTypes::TINY, MysqlTypes::SHORT, MysqlTypes::INT24, MysqlTypes::LONG], true)){
				$type = SqlColumnInfo::TYPE_INT;
				$columnFunc[$field->name] = "intval";
			}
			if(!isset($type)){
				$type = SqlColumnInfo::TYPE_OTHER;
			}
			$columns[$field->name] = new MysqlColumnInfo($field->name, $type, $field->flags, $field->type);
		}

		$rows = [];
		while(($row = $result->fetch_assoc()) !== null){
			foreach($row as $col => &$cell){
				if($cell !== null && isset($columnFunc[$col])){
					$cell = $columnFunc[$col]($cell);
				}
			}
			unset($cell);
			$rows[] = $row;
		}

		return new SqlSelectResult($columns, $rows);
	}

	protected function close(&$mysqli) : void{
		assert($mysqli instanceof mysqli);
		$mysqli->close();
	}

	public function getThreadName() : string{
		return __NAMESPACE__ . " connector #$this->slaveNumber";
	}
}
