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

namespace poggit\libasynql\base;

use Error;
use Exception;
use Generator;
use InvalidArgumentException;
use Logger;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Terminal;
use poggit\libasynql\DataConnector;
use poggit\libasynql\generic\GenericStatementFileParser;
use poggit\libasynql\GenericStatement;
use poggit\libasynql\libasynql;
use poggit\libasynql\result\SqlChangeResult;
use poggit\libasynql\result\SqlColumnInfo;
use poggit\libasynql\result\SqlInsertResult;
use poggit\libasynql\result\SqlSelectResult;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlThread;
use ReflectionClass;
use SOFe\AwaitGenerator\Await;
use TypeError;
use function array_fill;
use function array_merge;
use function array_pop;
use function count;
use function is_resource;
use function json_encode;
use function str_replace;
use function usleep;

class DataConnectorImpl implements DataConnector{
	/** @var Plugin */
	private $plugin;
	/** @var SqlThread */
	private $thread;
	/** @var Logger|null */
	private $logger;
	/** @var GenericStatement[] */
	private $queries = [];
	/** @var callable[] */
	private $handlers = [];
	/** @var int */
	private $queryId = 0;
	/** @var string|null */
	private $placeHolder;

	/**
	 * @param Plugin      $plugin
	 * @param SqlThread   $thread      the backend SqlThread to connect with
	 * @param null|string $placeHolder the backend-implementation-dependent placeholder. <code>"?"</code> for mysqli-based backends, <code>null</code> for PDO-based and SQLite3-based backends.
	 * @param bool        $logQueries
	 */
	public function __construct(Plugin $plugin, SqlThread $thread, ?string $placeHolder, bool $logQueries = false){
		$this->plugin = $plugin;
		if($thread instanceof SqlThreadPool){
			$thread->setDataConnector($this);
		}
		$this->thread = $thread;
		$this->logger = $logQueries ? $plugin->getLogger() : null;
		$this->placeHolder = $placeHolder;
	}

	public function setLoggingQueries(bool $loggingQueries) : void{
		$this->logger = $loggingQueries ? $this->plugin->getLogger() : null;
	}

	public function isLoggingQueries() : bool{
		return $this->logger !== null;
	}

	public function getLogger() : ?Logger{
		return $this->logger;
	}

	public function setLogger(?Logger $logger) : void{
		$this->logger = $logger;
	}

	public function loadQueryFile($fh, string $fileName = null) : void{
		if(!is_resource($fh)){
			throw new TypeError("Missing $fileName in resources directory of plugin.");
		}

		$parser = new GenericStatementFileParser($fileName, $fh);
		$parser->parse();
		foreach($parser->getResults() as $result){
			$this->loadQuery($result);
		}
	}

	public function loadQuery(GenericStatement $stmt) : void{
		if(isset($this->queries[$stmt->getName()])){
			throw new InvalidArgumentException("Duplicate GenericStatement: {$stmt->getName()}");
		}
		$this->queries[$stmt->getName()] = $stmt;
	}

	public function executeGeneric(string $queryName, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void{
		$this->executeImplLast($queryName, $args, SqlThread::MODE_GENERIC, static function() use ($onSuccess){
			if($onSuccess !== null){
				$onSuccess();
			}
		}, $onError);
	}

	public function asyncGeneric(string $queryName, array $args = []) : Generator{
		$onSuccess = yield Await::RESOLVE;
		$onError = yield Await::REJECT;
		$this->executeGeneric($queryName, $args, $onSuccess, $onError);
		yield Await::ONCE;
		return null;
	}

	public function executeChange(string $queryName, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void{
		$this->executeImplLast($queryName, $args, SqlThread::MODE_CHANGE, static function(SqlChangeResult $result) use ($onSuccess){
			if($onSuccess !== null){
				$onSuccess($result->getAffectedRows());
			}
		}, $onError);
	}

	public function asyncChange(string $queryName, array $args = []) : Generator{
		$onSuccess = yield Await::RESOLVE;
		$onError = yield Await::REJECT;
		$this->executeChange($queryName, $args, $onSuccess, $onError);
		$affectedRows = yield Await::ONCE;
		return $affectedRows;
	}

	public function executeInsert(string $queryName, array $args = [], ?callable $onInserted = null, ?callable $onError = null) : void{
		$this->executeImplLast($queryName, $args, SqlThread::MODE_INSERT, static function(SqlInsertResult $result) use ($onInserted){
			if($onInserted !== null){
				$onInserted($result->getInsertId(), $result->getAffectedRows());
			}
		}, $onError);
	}

	public function asyncInsert(string $queryName, array $args = []) : Generator{
		$onSuccess = yield Await::RESOLVE;
		$onError = yield Await::REJECT;
		$this->executeInsert($queryName, $args, static function(int $insertId, int $affectedRows) use($onSuccess) : void{
			$onSuccess([$insertId, $affectedRows]);
		}, $onError);
		$affectedRows = yield Await::ONCE;
		return $affectedRows;
	}

	public function executeSelect(string $queryName, array $args = [], ?callable $onSelect = null, ?callable $onError = null) : void{
		$this->executeImplLast($queryName, $args, SqlThread::MODE_SELECT, static function(SqlSelectResult $result) use ($onSelect){
			if($onSelect !== null){
				$onSelect($result->getRows(), $result->getColumnInfo());
			}
		}, $onError);
	}

	private function executeImplLast(string $queryName, array $args, int $mode, callable $handler, ?callable $onError) : void{
		$this->executeImpl($queryName, $args, $mode, static function($results) use($handler){
			$handler($results[count($results) - 1]);
		}, $onError);
	}

	public function executeMulti(string $queryName, array $args, int $mode, ?callable $handler = null, ?callable $onError = null) : void{
		$this->executeImpl($queryName, $args, $mode, static function($results) use($handler) {
			if($handler !== null){
				$handler($results);
			}
		}, $onError);
	}

	public function asyncSelect(string $queryName, array $args = []) : Generator{
		$onSuccess = yield Await::RESOLVE;
		$onError = yield Await::REJECT;
		$this->executeSelect($queryName, $args, static function(array $rows, array $columns) use($onSuccess) : void{
			$onSuccess($rows);
		}, $onError);
		$rows = yield Await::ONCE;
		return $rows;
	}

	public function asyncSelectWithInfo(string $queryName, array $args = []) : Generator{
		$onSuccess = yield Await::RESOLVE;
		$onError = yield Await::REJECT;
		$this->executeSelect($queryName, $args, static function(array $rows, array $columns) use($onSuccess) : void{
			$onSuccess([$rows, $columns]);
		}, $onError);
		$rows = yield Await::ONCE;
		return $rows;
	}

	private function executeImpl(string $queryName, array $args, int $mode, callable $handler, ?callable $onError) : void{
		if(!isset($this->queries[$queryName])){
			throw new InvalidArgumentException("The query $queryName has not been loaded");
		}

		$queries = $this->queries[$queryName]->format($args, $this->placeHolder, $outArgs);

		$modes = array_fill(0, count($queries), SqlThread::MODE_GENERIC);
		$modes[count($modes) - 1] = $mode;

		$this->executeImplRaw($queries, $outArgs, $modes, $handler, $onError);
	}

	/**
	 * @param string[] $queries
	 * @param mixed[][] $args
	 * @param int[] $modes
	 */
	public function executeImplRaw(array $queries, array $args, array $modes, callable $handler, ?callable $onError) : void{
		$queryId = $this->queryId++;
		$trace = libasynql::isPackaged() ? null : new Exception("(This is the original stack trace for the following error)");
		$this->handlers[$queryId] = function($results) use ($handler, $onError, $trace){
			if($results instanceof SqlError){
				$this->reportError($onError, $results, $trace);
			}else{
				try{
					$handler($results);
				}catch(Exception $e){
					if(!libasynql::isPackaged()){
						$prop = (new ReflectionClass(Exception::class))->getProperty("trace");
						$prop->setAccessible(true);
						$newTrace = $prop->getValue($e);
						$oldTrace = $prop->getValue($trace);
						for($i = count($newTrace) - 1, $j = count($oldTrace) - 1; $i >= 0 && $j >= 0 && $newTrace[$i] === $oldTrace[$j]; --$i, --$j){
							array_pop($newTrace);
						}
						/** @noinspection PhpUndefinedMethodInspection */
						$prop->setValue($e, array_merge($newTrace, [
							[
								"function" => Terminal::$COLOR_YELLOW . "--- below is the original stack trace ---" . Terminal::$FORMAT_RESET,
							],
						], $oldTrace));
					}
					throw $e;
				}catch(Error $e){
					if(!libasynql::isPackaged()){
						$exceptionProperty = (new ReflectionClass(Exception::class))->getProperty("trace");
						$exceptionProperty->setAccessible(true);
						$oldTrace = $exceptionProperty->getValue($trace);

						$errorProperty = (new ReflectionClass(Error::class))->getProperty("trace");
						$errorProperty->setAccessible(true);
						$newTrace = $errorProperty->getValue($e);

						for($i = count($newTrace) - 1, $j = count($oldTrace) - 1; $i >= 0 && $j >= 0 && $newTrace[$i] === $oldTrace[$j]; --$i, --$j){
							array_pop($newTrace);
						}
						/** @noinspection PhpUndefinedMethodInspection */
						$errorProperty->setValue($e, array_merge($newTrace, [
							[
								"function" => Terminal::$COLOR_YELLOW . "--- below is the original stack trace ---" . Terminal::$FORMAT_RESET,
							],
						], $oldTrace));
					}
					throw $e;
				}
			}
		};

		if($this->logger !== null){
			foreach($queries as $index => $query) {
				$mode = $modes[$index];
				$this->logger->debug("Queuing mode-$mode query: " . str_replace(["\r\n", "\n"], "\\n ", $query) . " | Args: " . json_encode($args[$index]));
			}
		}

		$this->thread->addQuery($queryId, $modes, $queries, $args);
	}

	private function reportError(?callable $default, SqlError $error, ?Exception $trace) : void{
		if($default !== null){
			try{
				$default($error, $trace);
				$error = null;
			}catch(SqlError $err){
				$error = $err;
			}
		}
		if($error !== null){
			$this->plugin->getLogger()->error($error->getMessage());
			if($error->getQuery() !== null){
				$this->plugin->getLogger()->debug("Query: " . $error->getQuery());
			}
			if($error->getArgs() !== null){
				$this->plugin->getLogger()->debug("Args: " . json_encode($error->getArgs()));
			}
			if($trace !== null){
				$this->plugin->getLogger()->debug("Stack trace: " . $trace->getTraceAsString());
			}
		}
	}

	public function waitAll() : void{
		while(!empty($this->handlers)){
			$this->checkResults();
			usleep(1000);
		}
	}

	public function checkResults() : void{
		$this->thread->readResults($this->handlers);
	}

	public function close() : void{
		$this->thread->stopRunning();
	}
}
