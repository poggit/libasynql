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

namespace libasynql;

use pocketmine\plugin\Plugin;

class ClearMysqlTask extends QueryMysqlTask{
	public static function closeAll(Plugin $plugin, MysqlCredentials $credentials){
		$scheduler = $plugin->getServer()->getScheduler();
		$size = $scheduler->getAsyncTaskPoolSize();
		for($i = 0; $i < $size; $i++){
			$scheduler->scheduleAsyncTaskToWorker(new ClearMysqlTask($credentials), $i);
		}
	}

	protected function execute(){
		$identifier = QueryMysqlTask::getIdentifier($this->getCredentials());
		$mysqli = $this->getFromThreadStore($identifier);
		if($mysqli instanceof \mysqli){
			$mysqli->close();
			$this->saveToThreadStore($identifier, null);
		}
	}
}
