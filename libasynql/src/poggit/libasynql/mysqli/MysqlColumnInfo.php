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

namespace poggit\libasynql\mysqli;

use poggit\libasynql\result\SqlColumnInfo;

class MysqlColumnInfo extends SqlColumnInfo{
	private $flags;
	private $mysqlType;

	public function __construct(string $name, string $type, int $flags, int $mysqlType){
		parent::__construct($name, $type);
		$this->flags = $flags;
		$this->mysqlType = $mysqlType;
	}

	public function getFlags() : int{
		return $this->flags;
	}

	public function hasFlag(int $flag) : bool{
		return ($this->flags & $flag) > 0;
	}

	public function getMysqlType() : int{
		return $this->mysqlType;
	}
}
