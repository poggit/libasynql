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

class DirectSqliteQueryTask extends SqliteQueryTask{
	/** @var string */
	private $query;
	/** @var string|array[] */
	private $args;

	public function __construct(string $file, string $query, array $args = [], callable $callback = null){
		parent::__construct($file, $callback);
		$this->query = $query;
		$this->args = serialize($args);
	}

	protected function execute(){
		$sqlite = $this->getSqlite();
		$args = unserialize($this->args);

		$taskResult = SqliteUtils::query($sqlite, $this->query, $args);

		$this->setResult($taskResult);
	}
}
