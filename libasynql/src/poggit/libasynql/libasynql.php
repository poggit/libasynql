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

namespace poggit\libasynql;

use InvalidArgumentException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Terminal;
use pocketmine\utils\Utils;
use poggit\libasynql\base\DataConnectorImpl;
use poggit\libasynql\base\SqlThreadPool;
use poggit\libasynql\mysqli\MysqlCredentials;
use poggit\libasynql\mysqli\MysqliThread;
use poggit\libasynql\sqlite3\Sqlite3Thread;
use function array_keys;
use function count;
use function extension_loaded;
use function implode;
use function is_array;
use function is_string;
use function strtolower;
use function usleep;

/**
 * An utility class providing convenient access to the API
 */
final class libasynql{
	/** @var bool */
	private static $packaged;

	public static function isPackaged() : bool{
		return self::$packaged;
	}

	public static function detectPackaged() : void{
		self::$packaged = __CLASS__ !== 'poggit\libasynql\libasynql';

		if(!self::$packaged && defined("pocketmine\\VERSION")){
			echo Terminal::$COLOR_YELLOW . "Warning: Use of unshaded libasynql detected. Debug mode is enabled. This may lead to major performance drop. Please use a shaded package in production. See https://poggit.pmmp.io/virion for more information.\n";
		}
	}

	private function __construct(){
	}

	/**
	 * Create a {@link DatabaseConnector} from a plugin and a config entry, and initializes it with the relevant SQL files according to the selected dialect
	 *
	 * @param PluginBase          $plugin     the plugin using libasynql
	 * @param mixed               $configData the config entry for database settings
	 * @param string[]|string[][] $sqlMap     an associative array with key as the SQL dialect ("mysql", "sqlite") and value as a string or string array indicating the relevant SQL files in the plugin's resources directory
	 * @param bool                $logQueries whether libasynql should log the queries with the plugin logger at the DEBUG level. Default <code>!libasynql::isPackaged()</code>.
	 *
	 * @return DataConnector
	 * @throws SqlError if the connection could not be created
	 */
	public static function create(PluginBase $plugin, $configData, array $sqlMap, bool $logQueries = null) : DataConnector{
		if(!is_array($configData)){
			throw new ConfigException("Database settings are missing or incorrect");
		}

		$type = (string) $configData["type"];
		if($type === ""){
			throw new ConfigException("Database type is missing");
		}

		if(count($sqlMap) === 0){
			throw new InvalidArgumentException('Parameter $sqlMap cannot be empty');
		}

		$pdo = ($configData["prefer-pdo"] ?? false) && extension_loaded("pdo");

		$dialect = null;
		$placeHolder = null;
		switch(strtolower($type)){
			case "sqlite":
			case "sqlite3":
			case "sq3":
				if(!$pdo && !extension_loaded("sqlite3")){
					throw new ExtensionMissingException("sqlite3");
				}

				$fileName = self::resolvePath($plugin->getDataFolder(), $configData["sqlite"]["file"] ?? "data.sqlite");
				if($pdo){
					// TODO add PDO support
				}else{
					$factory = Sqlite3Thread::createFactory($fileName);
				}
				$dialect = "sqlite";
				break;
			case "mysql":
			case "mysqli":
				if(!$pdo && !extension_loaded("mysqli")){
					throw new ExtensionMissingException("mysqli");
				}

				if(!isset($configData["mysql"])){
					throw new ConfigException("Missing MySQL settings");
				}

				$cred = MysqlCredentials::fromArray($configData["mysql"], strtolower($plugin->getName()));

				if($pdo){
					// TODO add PDO support
				}else{
					$factory = MysqliThread::createFactory($cred, $plugin->getServer()->getLogger());
					$placeHolder = "?";
				}
				$dialect = "mysql";

				break;
		}

		if(!isset($dialect, $factory, $sqlMap[$dialect])){
			throw new ConfigException("Unsupported database type \"$type\". Try \"" . implode("\" or \"", array_keys($sqlMap)) . "\".");
		}

		$pool = new SqlThreadPool($factory, $configData["worker-limit"] ?? 1);
		while(!$pool->connCreated()){
			usleep(1000);
		}
		if($pool->hasConnError()){
			throw new SqlError(SqlError::STAGE_CONNECT, $pool->getConnError());
		}

		$connector = new DataConnectorImpl($plugin, $pool, $placeHolder, $logQueries ?? !libasynql::isPackaged());
		foreach(is_string($sqlMap[$dialect]) ? [$sqlMap[$dialect]] : $sqlMap[$dialect] as $file){
			$resource = $plugin->getResource($file);
			if($resource===null){
				throw new InvalidArgumentException("resources/$file does not exist");
			}
			$connector->loadQueryFile($resource);
		}

		return $connector;
	}

	private static function resolvePath(string $folder, string $path) : string{
		if($path[0] === "/"){
			return $path;
		}
		if(Utils::getOS() === "win"){
			if($path[0] === "\\" || $path[1] === ":"){
				return $path;
			}
		}
		return $folder . $path;
	}
}

/**
 * An empty function accepting void parameters and returning void. Can be used as a dummy function.
 */
function nop() : void{

}

libasynql::detectPackaged();
