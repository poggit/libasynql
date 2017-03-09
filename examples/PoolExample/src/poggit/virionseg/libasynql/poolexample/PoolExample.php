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

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use libasynql\MysqlCredentials;
use libasynql\DirectQueryMysqlTask;
use libasynql\result\MysqlResult;
use libasynql\result\MysqlSelectResult;

class PoolExample extends PluginBase implements Listener{
	private $mysqlCredentials;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->mysqlCredentials = MysqlCredentials::fromArray($this->getConfig()->get("mysql"));
		$task = new DirectQueryMysqlTask($this->mysqlCredentials,
			"CREATE TABLE IF NOT EXISTS players (
				username VARCHAR(16) PRIMARY KEY,
				registerTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				lastJoin TIMESTATMP DEFAULT CURRENT_TIMESTAMP
			)", [],
			function (){
				// event handlers won't work until the database has been prepared
				$this->getServer()->getPluginManager()->registerEvents($this, $this);
			});
		$this->getServer()->getScheduler()->scheduleAsyncTask($task);
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onJoin(PlayerJoinEvent $event){
		$name = strtolower($event->getPlayer()->getName());
		$task = new DirectQueryMysqlTask($this->mysqlCredentials,
			"SELECT UNIX_TIMESTAMP(registerTime) AS reg, UNIX_TIMESTAMP(lastJoin) AS lj FROM players WHERE username = ?", [["s", $name]],
			function (MysqlResult $result) use ($name){
				if($this->isDisabled()){
					return;
				}
				if($result instanceof MysqlSelectResult){
					$task = new DirectQueryMysqlTask($this->mysqlCredentials,
						"INSERT INTO players (username) VALUES (?) ON DUPLICATE KEY UPDATE lastJoin = CURRENT_TIMESTAMP ", [["s", $name]]);
					$this->getServer()->getScheduler()->scheduleAsyncTask($task);
					if(count($result->rows) === 0){
						$this->getServer()->broadcastMessage("$name is a new player!");
					}else{
						$result->fixTypes(["reg" => MysqlSelectResult::TYPE_INT, "lj" => MysqlSelectResult::TYPE_INT]);
						$row = $result->rows[0];
						$this->getServer()->broadcastMessage(sprintf("%s is an old player, registered on %s and last joined on %s",
							$name, date("Y-m-d H:i:s", $row["reg"]), date("Y-m-d H:i:s", $row["lj"])));
					}
				}
			});
		$this->getServer()->getScheduler()->scheduleAsyncTask($task);
	}
}
