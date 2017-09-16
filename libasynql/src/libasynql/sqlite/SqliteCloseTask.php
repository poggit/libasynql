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

namespace libasynql\sqlite;

class SqliteCloseTask extends SqliteQueryTask{
	protected function execute(){
		$identifier = SqliteQueryTask::getIdentifier($this->getFile());
		$sqlite = $this->getFromThreadStore($identifier);
		if($sqlite instanceof \mysqli){
			$sqlite->close();
			$this->saveToThreadStore($identifier, null);
		}
	}
}
