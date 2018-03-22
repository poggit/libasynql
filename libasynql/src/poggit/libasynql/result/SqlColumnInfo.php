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

namespace poggit\libasynql\result;

class SqlColumnInfo{
	public const TYPE_STRING = "string";
	public const TYPE_INT = "int";
	public const TYPE_FLOAT = "float";
	public const TYPE_TIMESTAMP = "timestamp";
	public const TYPE_BOOL = "bool";
	public const TYPE_NULL = "null";
	public const TYPE_OTHER = "unknown";

	private $name;
	private $type;

	public function __construct(string $name, string $type){
		$this->name = $name;
		$this->type = $type;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getType() : string{
		return $this->type;
	}
}
