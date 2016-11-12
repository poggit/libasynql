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

namespace poggit\virions\libasynql\pool\result;

class MysqlSelectResult extends MysqlSuccessResult{
	const TYPE_STRING = 1; // CHAR, BINARY, BLOB
	const TYPE_INT = 2; // INT
	const TYPE_FLOAT = 3; // DECIMAL, FLOAT, DOUBLE
	const TYPE_BOOL = 4; // BIT(1)

	static $DEFAULTS = [
		self::TYPE_STRING => "",
		self::TYPE_INT => 0,
		self::TYPE_FLOAT => 0.0,
		self::TYPE_BOOL => false
	];

	/** @var array[] */
	public $rows;

	public function fixTypes(array $columns){
		foreach($this->rows as &$row){
			foreach($columns as $column => $type){
				if(!isset($row[$column])){
					$row[$column] = self::$DEFAULTS[$type];
				}else{
					switch($type){
						case self::TYPE_STRING:
							$row[$column] = (string) $row[$column];
							break;
						case self::TYPE_INT:
							$row[$column] = (int) $row[$column];
							break;
						case self::TYPE_FLOAT:
							$row[$column] = (float) $row[$column];
							break;
						case self::TYPE_BOOL:
							$row[$column] = (bool) (int) $row[$column];
							break;
					}
				}
			}
		}
	}
}
