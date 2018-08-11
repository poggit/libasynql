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

namespace poggit\libasynql;

use pocketmine\utils\TextFormat;
use RuntimeException;
use function file;
use function is_file;
use function php_ini_loaded_file;
use function strpos;

class ExtensionMissingException extends RuntimeException{
	public function __construct(string $extensionName){
		$instructions = "Please install PHP according to the instructions from http://pmmp.readthedocs.io/en/rtfd/installation.html which provides the $extensionName extension.";

		$ini = php_ini_loaded_file();
		if($ini && is_file($ini)){
			foreach(file($ini) as $i => $line){
				if(strpos($line, ";extension=") !== false && stripos($line, $extensionName) !== false){
					$instructions = TextFormat::GOLD . "Please remove the leading semicolon on line " . ($i + 1) . " of $ini and restart the server " . TextFormat::RED . "so that the $extensionName extension can be loaded.";
				}
			}
		}

		parent::__construct("The $extensionName extension is missing. $instructions");
	}
}
