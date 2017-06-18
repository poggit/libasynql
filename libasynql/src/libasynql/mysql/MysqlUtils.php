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
use libasynql\result\SqlSelectResult;
use libasynql\result\SqlSuccessResult;
use mysqli;
use pocketmine\plugin\Plugin;

class MysqlUtils{
	/**
	 * This value can be changed to configure whether queries executed through MysqlUtils should be logged.
	 *
	 * Logged queries are echoed to the terminal directly and does not pass through the PocketMine Logger interface.
	 *
	 * This value may not be consistent across threads. Set this value in every AsyncTask execution that uses query
	 * functions in this class.
	 *
	 * Modifying this value will not lead to race condition with AsyncTasks executing on other async workers. However,
	 * it is considered undefined whether changes to this value will apply in other AsyncTask executions.
	 *
	 * @var bool
	 */
	public static $LOG_QUERIES = false;

	/**
	 * @param mysqli   $mysqli the MySQL connection
	 * @param string   $table  the name of the table (and optionally, with the schema name). If it needs to be escaped,
	 *                         it is the caller's responsibility to add the backticks.
	 * @param string[] $types  an array of column name => param type (one of <code>idsb</code>). length of this array is
	 *                         the number of columns to insert. column names are automatically escaped.
	 * @param mixed[]  $keys   the keys passed to the mapper function. length of this array is the number of rows to
	 *                         insert
	 * @param callable $mapper a function that accepts an element from $keys and returns an associative array with
	 *                         column names as keys and respective values of that row as values
	 */
	public static function bulkInsert(mysqli $mysqli, string $table, array $types, array $keys, callable $mapper){
		assert(array_reduce($types, function($bool, $val){
			return $bool && strpos("idsb", $val) !== false;
		}, true), '$types should only contain "i", "d", "s" or "b"');
		$cols = implode(",", array_keys($types));
		$rowFormat = ",(" . substr(str_repeat(",?", count($types)), 1) . ")";
		$rows = substr(str_repeat($rowFormat, count($keys)), 1);
		$query = "INSERT INTO $table ($cols) VALUES $rows";
		$args = [];
		foreach($keys as $key){
			$row = $mapper($key);
			assert(array_keys($row) == array_keys($types)); // The == operator is order-insensitive
			foreach($row as $col => $val){
				$args[] = [$types[$col], $val];
			}
		}
		MysqlUtils::equery($mysqli, $query, $args);
	}

	/**
	 * Same as {@see MysqlUtils::equery} except that it asserts the result to be a SqlSelectResult.
	 *
	 * @param mysqli  $mysqli the MySQL connection
	 * @param string  $query  the query string
	 * @param array[] $args   an array of args in the format
	 *                        <code>[["type of arg 1", "value of arg 1"], ["type of arg 2", "value of arg 2"], ...]</code>
	 *
	 * @return SqlSelectResult
	 * @throws SqlException
	 */
	public static function squery(mysqli $mysqli, string $query, array $args) : SqlSelectResult{
		$result = MysqlUtils::equery($mysqli, $query, $args);
		assert($result instanceof SqlSelectResult);
		return $result;
	}

	/**
	 * Same as {@see MysqlUtils::query} except that it throws an exception upon unsuccessful result.
	 *
	 * @param mysqli  $mysqli the MySQL connection
	 * @param string  $query  the query string
	 * @param array[] $args   an array of args in the format
	 *                        <code>[["type of arg 1", "value of arg 1"], ["type of arg 2", "value of arg 2"], ...]</code>
	 *
	 * @return SqlSuccessResult
	 * @throws SqlException
	 */
	public static function equery(mysqli $mysqli, string $query, array $args) : SqlSuccessResult{
		$result = MysqlUtils::query($mysqli, $query, $args);
		if($result instanceof SqlSuccessResult){
			return $result;
		}else{
			assert($result instanceof SqlErrorResult);
			throw $result->getException();
		}
	}

	/**
	 * A convenience method that executes the specified query.
	 *
	 * @param mysqli  $mysqli the MySQL connection
	 * @param string  $query  the query string
	 * @param array[] $args   an array of args in the format
	 *                        <code>[["type of arg 1", "value of arg 1"], ["type of arg 2", "value of arg 2"], ...]</code>
	 *
	 * @return SqlResult
	 */
	public static function query(mysqli $mysqli, string $query, array $args) : SqlResult{
		$start = microtime(true);
		try{
			if(MysqlUtils::$LOG_QUERIES){
				echo date("[H:i:s]"), "Executing libasynql query @ ", __NAMESPACE__, ": $query, args = ", json_encode($args), "\n";
			}
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

	public static function versionDatabase(mysqli $mysqli, $decl, bool $close = false){
		$tablePrefix = $decl["tablePrefix"] ?? "";
		$metaTable = $tablePrefix . ($decl["metaTable"] ?? "metadata");
		MysqlUtils::equery($mysqli, "CREATE TABLE IF NOT EXISTS `$metaTable` (name VARCHAR(20) PRIMARY KEY , value VARCHAR(20))", []);

		$versions = $decl["versions"];
		uksort($versions, function($a, $b){ // this only works in the UK!
			return -version_compare($a, $b);
		});
		$myVersion = array_keys($versions)[0];

		$dbVersion = MysqlUtils::squery($mysqli, "SELECT value FROM $metaTable WHERE name = 'libasynql.version'", [])->rows[0]["value"] ?? null;
		if($dbVersion === null){
			MysqlUtils::equery($mysqli, "INSERT INTO $metaTable (name, value) VALUES ('libasynql.version', ?)", [["s", $myVersion]]);
			// TODO db setup
		}else{
			if(!isset($versions[$dbVersion])){
				throw new \UnexpectedValueException("The database is using an unsupported version: $dbVersion");
			}
			if($dbVersion !== $myVersion){
				$myVersionStruct = $versions[$myVersion];
				$dbVersionStruct = $versions[$dbVersion];

				// TODO db alter
			}
		}

		if($close){
			$mysqli->close();
		}
	}
}
