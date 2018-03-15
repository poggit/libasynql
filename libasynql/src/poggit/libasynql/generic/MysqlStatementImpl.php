<?php

/*
 * libasynql_v3
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

namespace poggit\libasynql\generic;

use RuntimeException;
use function assert;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

class MysqlStatementImpl extends GenericStatementImpl{
	public function getDialect() : string{
		return "mysql";
	}

	protected function formatVariable(GenericVariable $variable, $value) : ?string{
		switch($variable->getType()){
			case GenericVariable::TYPE_BOOL:
				assert(is_bool($value));
				return $value ? "1" : "0";

			case GenericVariable::TYPE_INT:
				assert(is_int($value));
				return (string) $value;

			case GenericVariable::TYPE_FLOAT:
				assert(is_float($value));
				return (string) $value;

			case GenericVariable::TYPE_STRING:
				assert(is_string($value));
				return null;

			case GenericVariable::TYPE_TIMESTAMP:
				assert(is_int($value) || is_float($value));
				if($value === GenericVariable::TIME_NOW){
					return "CURRENT_TIMESTAMP";
				}
				return "FROM_UNIXTIME($value)";
		}

		throw new RuntimeException("Unsupported variable type");
	}
}
