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

namespace poggit\virionseg\libasynql\poolexample;

use libasynql\mysql\DirectMysqlQueryTask;
use libasynql\mysql\MysqlCredentials;
use libasynql\mysql\MysqlUtils;
use libasynql\result\SqlResult;
use libasynql\result\SqlSelectResult;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class PoolExample extends PluginBase implements Listener{
	private $mysqlCredentials;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->mysqlCredentials = MysqlCredentials::fromArray($this->getConfig()->get("mysql"));
		MysqlUtils::init($this, $this->mysqlCredentials);
		$r = $this->getResource("database.yml");
		$data = stream_get_contents($r);
		fclose($r);
		MysqlUtils::versionDatabase($this->mysqlCredentials->newMysqli(), yaml_parse($data), true);
	}

	public function onDisable(){
		MysqlUtils::closeAll($this, $this->mysqlCredentials);
	}

	/**
	 * @param PlayerJoinEvent $event
	 *
	 * @priority MONITOR
	 */
	public function onJoin(PlayerJoinEvent $event){
		$name = strtolower($event->getPlayer()->getName());
		$task = new DirectMysqlQueryTask($this->mysqlCredentials,
			"SELECT UNIX_TIMESTAMP(registerTime) AS reg, UNIX_TIMESTAMP(lastJoin) AS lj FROM players WHERE username = ?", [["s", $name]],
			function(SqlResult $result) use ($name){
				if($this->isDisabled()){
					return;
				}
				if($result instanceof SqlSelectResult){
					$task = new DirectMysqlQueryTask($this->mysqlCredentials,
						"INSERT INTO players (username) VALUES (?) ON DUPLICATE KEY UPDATE lastJoin = CURRENT_TIMESTAMP ", [["s", $name]]);
					$this->getServer()->getScheduler()->scheduleAsyncTask($task);
					if(count($result->rows) === 0){
						$this->getServer()->broadcastMessage("$name is a new player!");
					}else{
						$result->fixTypes([
							"reg" => SqlSelectResult::TYPE_INT,
							"lj" => SqlSelectResult::TYPE_INT
						]);
						$row = $result->rows[0];
						$this->getServer()->broadcastMessage(sprintf("%s is an old player, registered on %s and last joined on %s",
							$name, date("Y-m-d H:i:s", $row["reg"]), date("Y-m-d H:i:s", $row["lj"])));
					}
				}
			});
		$this->getServer()->getScheduler()->scheduleAsyncTask($task);
	}
}
