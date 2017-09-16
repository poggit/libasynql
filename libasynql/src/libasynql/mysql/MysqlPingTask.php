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

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class MysqlPingTask extends PluginTask{
	private $credentials;

	/**
	 * @internal PingMysqlTask constructor.
	 *
	 * @param Plugin           $owner
	 * @param MysqlCredentials $credentials
	 */
	public function __construct(Plugin $owner, MysqlCredentials $credentials){
		parent::__construct($owner);
		$this->credentials = $credentials;
	}

	public function onRun(int $currentTick){
		$scheduler = $this->getOwner()->getServer()->getScheduler();
		$size = $scheduler->getAsyncTaskPoolSize();
		$credentials = $this->credentials;
		for($i = 0; $i < $size; $i++){
			$scheduler->scheduleAsyncTaskToWorker(new class($credentials) extends MysqlQueryTask{
				protected function execute(){
					$identifier = MysqlQueryTask::getIdentifier($this->getCredentials());
					/** @var \mysqli|null $mysqli */
					$mysqli = $this->getFromThreadStore($identifier);
					if($mysqli !== null){
						$mysqli->ping();
					}
				}
			}, $i);
		}
	}
}
