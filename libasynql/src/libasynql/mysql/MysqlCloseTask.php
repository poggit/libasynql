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

class MysqlCloseTask extends MysqlQueryTask{
	protected function execute(){
		$identifier = MysqlQueryTask::getIdentifier($this->getCredentials());
		$mysqli = $this->getFromThreadStore($identifier);
		if($mysqli instanceof \mysqli){
			$mysqli->close();
			$this->saveToThreadStore($identifier, null);
		}
	}
}
