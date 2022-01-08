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
use InvalidStateException;
use JsonSerializable;
use function assert;
use function in_array;
use function is_string;
use function json_decode;
use function stripos;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;

/**
 * Represents a variable that can be passed into {@link GenericStatement::format()}
 */
class GenericVariable implements JsonSerializable{
	public const TYPE_STRING = "string";
	public const TYPE_INT = "int";
	public const TYPE_FLOAT = "float";
	public const TYPE_BOOL = "bool";
	public const TYPE_TIMESTAMP = "timestamp";

	public const TIME_0 = "0";
	public const TIME_NOW = "NOW";

	protected $name;
	protected $list = false;
	protected $canEmpty = false;
	protected $nullable = false;
	protected $type;
	/** @var string|int|float|bool|null */
	protected $default = null;

	public function __construct(string $name, string $type, ?string $default){
		if(strpos($name, ":") !== false){
			throw new InvalidArgumentException("Colon is disallowed in a variable name");
		}
		$this->name = $name;
		if(stripos($type, "list:") === 0){
			$this->list = true;
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$type = substr($type, strlen("list:"));
		}elseif(stripos($type, "list?") === 0){
			$this->list = true;
			$this->canEmpty = true;
			/** @noinspection CallableParameterUseCaseInTypeContextInspection */
			$type = substr($type, strlen("list?"));
		}elseif($type[0] === "?"){
			$this->nullable = true;
			$type = substr($type, 1);
		}
		$this->type = $type;
		if($default !== null){
			if($this->list){
				throw new InvalidArgumentException("Lists cannot have default value");
			}
			switch($type){
				case self::TYPE_STRING:
					if($default[0] === "\"" && $default[strlen($default) - 1] === "\""){
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
					if(!in_array(strtoupper($default), [
						self::TIME_NOW,
						self::TIME_0,
					], true)){
						throw new InvalidArgumentException("Invalid timestamp default");
					}
					$this->default = $default;
					break;

				default:
					throw new InvalidArgumentException("Unknown type \"$type\"");
			}
		}
	}

	public function unlist() : GenericVariable{
		if(!$this->list){
			throw new InvalidStateException("Cannot unlist a non-list variable");
		}
		$clone = clone $this;
		$clone->list = false;
		return $clone;
	}

	public function getName() : string{
		return $this->name;
	}

	public function isList() : bool{
		return $this->list;
	}

	/**
	 * Returns whether the list variable is declared with <code>list?</code> rather than <code>list:</code>.
	 *
	 * If the SQL dialect does not support empty list declarations <code>()</code>, and <code>list:</code> is used, an exception will be thrown when an empty array is passed as the value. If <code>list?</code> is used, a randomly-generated string will be filled into the array to satisfy the language's requirements. This might cause undesired behaviour unless you are only using this variable for a simple <code>IN :list</code> condition.
	 *
	 * As this may expose a security breach or a performance degrade, plugins are not encouraged to use this method. Instead it is more desirable to check if the array is empty before passing the value into libasynql.
	 *
	 * @return bool
	 */
	public function canBeEmpty() : bool{
		if(!$this->list){
			throw new InvalidStateException("canBeEmpty() is only available for list variables");
		}

		return $this->canEmpty;
	}

	public function isNullable() : bool{
		return $this->nullable;
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

	public function equals(GenericVariable $that, &$diff = null) : bool{
		if($this->name !== $that->name){
			$diff = "name";
			return false;
		}
		if($this->list !== $that->list){
			$diff = "isList";
			return false;
		}
		if($this->canEmpty !== $that->canEmpty){
			$diff = "canBeEmpty";
			return false;
		}
		if($this->type !== $that->type){
			$diff = "type";
			return false;
		}
		if($this->default !== $that->default){
			$diff = "defaultValue";
			return false;
		}
		return true;
	}

	public function jsonSerialize(){
		return [
			"name" => $this->name,
			"isList" => $this->list,
			"canEmpty" => $this->canEmpty,
			"type" => $this->type,
			"default" => $this->default,
		];
	}
}
