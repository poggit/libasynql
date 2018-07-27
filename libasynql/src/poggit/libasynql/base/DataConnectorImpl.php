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
use InvalidArgumentException;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Terminal;
use poggit\libasynql\CallbackTask;
use poggit\libasynql\DataConnector;
use poggit\libasynql\generic\GenericStatementFileParser;
use poggit\libasynql\GenericStatement;
use poggit\libasynql\libasynql;
use poggit\libasynql\result\SqlChangeResult;
use poggit\libasynql\result\SqlInsertResult;
use poggit\libasynql\result\SqlSelectResult;
use poggit\libasynql\SqlError;
use poggit\libasynql\SqlThread;
use ReflectionClass;
use function array_merge;
use function array_pop;
use function count;
use function json_encode;
use function str_replace;
use function usleep;

class DataConnectorImpl implements DataConnector{
	/** @var Plugin */
	private $plugin;
	/** @var SqlThread */
	private $thread;
	/** @var bool */
	private $loggingQueries;
	/** @var GenericStatement[] */
	private $queries = [];
	private $handlers = [];
	private $queryId = 0;
	/** @var string|null */
	private $placeHolder;
	private $task;

	/**
	 * @param Plugin      $plugin
	 * @param SqlThread   $thread      the backend SqlThread to connect with
	 * @param null|string $placeHolder the backend-implementation-dependent placeholder. <code>"?"</code> for mysqli-based backends, <code>null</code> for PDO-based and SQLite3-based backends.
	 * @param bool        $logQueries
	 */
	public function __construct(Plugin $plugin, SqlThread $thread, ?string $placeHolder, bool $logQueries = false){
		$this->plugin = $plugin;
		$this->thread = $thread;
		$this->loggingQueries = $logQueries;
		$this->placeHolder = $placeHolder;

		$this->task = new CallbackTask([$this, "checkResults"]);
		$this->plugin->getScheduler()->scheduleRepeatingTask($this->task, 1);
	}

	public function setLoggingQueries(bool $loggingQueries) : void{
		$this->loggingQueries = !libasynql::isPackaged() && $loggingQueries;
	}

	public function isLoggingQueries() : bool{
		return $this->loggingQueries;
	}

	public function loadQueryFile($fh, string $fileName = null) : void{
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
		$this->executeImpl($queryName, $args, SqlThread::MODE_GENERIC, function() use ($onSuccess){
			if($onSuccess !== null){
				$onSuccess();
			}
		}, $onError);
	}

	public function executeChange(string $queryName, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void{
		$this->executeImpl($queryName, $args, SqlThread::MODE_CHANGE, function(SqlChangeResult $result) use ($onSuccess){
			if($onSuccess !== null){
				$onSuccess($result->getAffectedRows());
			}
		}, $onError);
	}

	public function executeInsert(string $queryName, array $args = [], ?callable $onInserted = null, ?callable $onError = null) : void{
		$this->executeImpl($queryName, $args, SqlThread::MODE_INSERT, function(SqlInsertResult $result) use ($onInserted){
			if($onInserted !== null){
				$onInserted($result->getInsertId(), $result->getAffectedRows());
			}
		}, $onError);
	}

	public function executeSelect(string $queryName, array $args = [], ?callable $onSelect = null, ?callable $onError = null) : void{
		$this->executeImpl($queryName, $args, SqlThread::MODE_SELECT, function(SqlSelectResult $result) use ($onSelect){
			if($onSelect !== null){
				$onSelect($result->getRows(), $result->getColumnInfo());
			}
		}, $onError);
	}

	private function executeImpl(string $queryName, array $args, int $mode, callable $handler, ?callable $onError) : void{
		$queryId = $this->queryId++;
		$trace = libasynql::isPackaged() ? null : new Exception("(This is the original stack trace for the following error)");
		$this->handlers[$queryId] = function($result) use ($handler, $onError, $trace){
			if($result instanceof SqlError){
				$this->reportError($onError, $result, $trace);
			}else{
				try{
					$handler($result);
				}catch(Exception $e){
					if(!libasynql::isPackaged()){
						$prop = (new ReflectionClass(Exception::class))->getProperty("trace");
						$prop->setAccessible(true);
						$newTrace = $prop->getValue($e);
						$oldTrace = $prop->getValue($trace);
						for($i = count($newTrace) - 1, $j = count($oldTrace) - 1; $i >= 0 && $j >= 0 && $newTrace[$i] === $oldTrace[$j]; --$i, --$j){
							array_pop($newTrace);
						}
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

		if(!isset($this->queries[$queryName])){
			throw new InvalidArgumentException("The query $queryName has not been loaded");
		}
		$query = $this->queries[$queryName]->format($args, $this->placeHolder, $outArgs);

		if($this->loggingQueries){
			$this->plugin->getLogger()->debug("Queuing mode-$mode query: " . str_replace(["\r\n", "\n"], "\\n ", $query) . " | Args: " . json_encode($outArgs));
		}

		$this->thread->addQuery($queryId, $mode, $query, $outArgs);
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
		$this->plugin->getScheduler()->cancelTask($this->task->getTaskId());
	}
}
