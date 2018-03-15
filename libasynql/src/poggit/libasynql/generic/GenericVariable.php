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

use InvalidArgumentException;
use function assert;
use function in_array;
use function is_string;
use function json_decode;
use function strlen;

/**
 * Represents a variable that can be passed into {@link GenericStatement::format()}
 */
class GenericVariable{
	public const TYPE_STRING = "string";
	public const TYPE_INT = "int";
	public const TYPE_FLOAT = "float";
	public const TYPE_BOOL = "bool";
	public const TYPE_TIMESTAMP = "timestamp";

	public const TIME_0 = "0";
	public const TIME_NOW = "NOW";

	protected $name;
	protected $type;
	/** @var string|int|float|bool|null */
	protected $default = null;

	public function __construct(string $name, string $type, ?string $default){
		if(strpos($name, ":") !== false){
			throw new InvalidArgumentException("Colon is disallowed in a variable name");
		}
		$this->name = $name;
		$this->type = $type;
		if($default !== null){
			switch($type){
				case self::TYPE_STRING:
					if($default{0} === "\"" && $default{strlen($default) - 1} === "\""){
						$default = json_decode($default);
						assert(is_string($default));
					}
					$this->default = $default;
					break;

				case self::TYPE_INT:
					$this->default = (int) $default;
					break;

				case self::TYPE_FLOAT:
					$this->default = (float) $default;
					break;

				case self::TYPE_BOOL:
					$this->default = in_array($default, ["true", "on", "1"], true);
					break;

				case self::TYPE_TIMESTAMP:
					if(!in_array($default, [
						self::TIME_NOW,
						self::TIME_0,
					], true)){
						throw new InvalidArgumentException("Invalid timestamp default");
					}
					$this->default = $default;

				default:
					throw new InvalidArgumentException("Unknown type \"$type\"");
			}
		}
	}

	public function getName() : string{
		return $this->name;
	}

	public function getType() : string{
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function getDefault(){
		return $this->default;
	}

	public function isOptional() : bool{
		return $this->default !== null;
	}

	public function format($value, ?string $placeHolder, array &$outArgs) : string{
		if($placeHolder !== null){
			$outArgs[] = $value ?? $this->default;
			return $placeHolder;
		}

		// TODO wtf was I doing
	}
}
