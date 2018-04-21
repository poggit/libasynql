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

namespace poggit\libasynql\generic;

use InvalidArgumentException;
use RuntimeException;
use function array_map;
use function assert;
use function bin2hex;
use function implode;
use function is_array;
use function is_bool;
use function is_finite;
use function is_float;
use function is_int;
use function is_string;
use function rand;
use function random_bytes;

class MysqlStatementImpl extends GenericStatementImpl{
	public function getDialect() : string{
		return "mysql";
	}

	protected function formatVariable(GenericVariable $variable, $value, ?string $placeHolder, array &$outArgs) : string{
		if($variable->isList()){
			assert(is_array($value));
			if(empty($value)){
				if(!$variable->canBeEmpty()){
					throw new InvalidArgumentException("Cannot pass an empty array for :{$variable->getName()}");
				}

				return "('" . bin2hex(random_bytes(20)) . ")";
			}

			$unlist = $variable->unlist();
			return "(" . implode(",", array_map(function($value) use ($unlist, $placeHolder, &$outArgs){
					return $this->formatVariable($unlist, $value, $placeHolder, $outArgs);
				}, $value)) . ")";
		}

		if($value === null){
			if(!$variable->isNullable()){
				throw new InvalidArgumentException("The variable :{$variable->getName()} is not nullable");
			}
			return "NULL";
		}

		switch($variable->getType()){
			case GenericVariable::TYPE_BOOL:
				assert(is_bool($value));
				return $value ? "1" : "0";

			case GenericVariable::TYPE_INT:
				assert(is_int($value));
				return (string) $value;

			case GenericVariable::TYPE_FLOAT:
				assert(is_int($value) || is_float($value));
				if(!is_finite($value)){
					throw new InvalidArgumentException("Cannot encode $value in MySQL");
				}
				return (string) $value;

			case GenericVariable::TYPE_STRING:
				assert(is_string($value));
				if($placeHolder !== null){
					$outArgs[] = $value;
					return $placeHolder;
				}

				do{
					$varName = ":var" . rand(0, 10000000);
				}while(isset($outArgs[$varName]));
				$outArgs[$varName] = $value;
				return " " . $varName . " ";

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
