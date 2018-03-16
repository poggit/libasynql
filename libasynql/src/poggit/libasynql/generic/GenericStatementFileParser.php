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

use poggit\libasynql\GenericStatement;
use function array_pop;
use function assert;
use function count;
use function fclose;
use function feof;
use function fgets;
use function implode;
use function ltrim;
use function preg_split;
use function strpos;
use function substr;
use function trim;
use const PREG_SPLIT_NO_EMPTY;
use const PREG_SPLIT_OFFSET_CAPTURE;

class GenericStatementFileParser{
	/** @var resource */
	private $fh;
	/** @var int */
	private $lineNo = 0;

	/** @var string[] */
	private $identifierStack = [];
	/** @var bool */
	private $parsingQuery = false;
	/** @var GenericVariable[] */
	private $variables = [];
	/** @var string[] */
	private $buffer = [];

	/** @var string|null */
	private $knownDialect = null;
	/** @var GenericStatement[] */
	private $results = [];

	/**
	 * @param resource $fh
	 */
	public function __construct($fh){
		$this->fh = $fh;
	}

	/**
	 * Parses the file, and closes the stream.
	 *
	 * @throws GenericStatementFileParseException if the file contains a syntax error or compile error
	 */
	public function parse() : void{
		try{
			while(!feof($this->fh)){
				$this->readLine();
			}
			if(!empty($this->identifierStack)){
				$this->error("Unexpected end of file, " . count($this->identifierStack) . " groups not closed");
			}
		}finally{
			fclose($this->fh);
		}
	}

	/**
	 * @return GenericStatement[]
	 */
	public function getResults() : array{
		return $this->results;
	}

	private function readLine() : void{
		++$this->lineNo;
		$line = trim(fgets($this->fh));

		if($line === ""){
			return;
		}

		if($this->tryCommand($line)){
			return;
		}

		if(empty($this->identifierStack)){
			$this->error("Unexpected query text; start a query with { first");
		}
		$this->buffer[] = $line;
		$this->parsingQuery = true;
	}

	private function tryCommand(string $line) : bool{
		if(strpos($line, "-- #") !== 0){
			return false;
		}

		$line = ltrim(substr($line, 4));
		$cmd = $line{0};
		$args = [];
		$argOffsets = [];
		$regex = /** @lang RegExp */
			'/[ \t]/';
		foreach(preg_split($regex, substr($line, 1), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE) as [$arg, $offset]){
			$args[] = $arg;
			$argOffsets[] = $offset;
		}

		switch($cmd){
			case "!":
				// dialect command
				if($this->knownDialect !== null){
					$this->error("Dialect declared more than once");
				}

				if(!isset($args[0])){
					$this->error("Missing operand: DIALECT");
				}

				$this->knownDialect = $args[0];
				return true;

			case "{":
				// start group/query command
				if($this->knownDialect === null){
					$this->error("Dialect declaration must be the very first line");
				}

				if($this->parsingQuery){
					$this->error("Unexpected {, close previous query first");
				}

				if(!isset($args[0])){
					$this->error("Missing operand: IDENTIFIER_NAME");
				}

				$this->identifierStack[] = $args[0];
				return true;

			case "}":
				// end group/query command

				if(count($this->identifierStack) === 0){
					$this->error("No matching { for }");
				}

				if($this->parsingQuery){
					if(count($this->buffer) === 0){
						$this->error("Variables are declared but no query is provided");
					}

					$query = implode("\n", $this->buffer);
					assert($this->knownDialect !== null);
					$stmt = GenericStatementImpl::forDialect($this->knownDialect, implode(".", $this->identifierStack), $query, $this->variables);
					$this->variables = [];
					$this->buffer = [];

					if(isset($this->results[$stmt->getName()])){
						$this->error("Duplicate query name ({$stmt->getName()})");
					}
					$this->results[$stmt->getName()] = $stmt;
				} // end query

				array_pop($this->identifierStack);
				return true;

			case ":":
				if(empty($this->identifierStack)){
					$this->error("Unexpected variable declaration; start a query with { first");
				}

				if(!isset($args[1])){
					$this->error("Missing operand: VAR_TYPE");
				}

				$var = new GenericVariable($args[0], $args[1], isset($args[2]) ? substr($line, $argOffsets[2] + 1) : null);
				if(isset($this->variables[$var->getName()])){
					$this->error("Duplicate variable definition of :{$var->getName()}");
				}
				$this->variables[$var->getName()] = $var;
				$this->parsingQuery = true;
				return true;
		}

		return false;
	}

	/**
	 * @param string $problem
	 * @throw GenericStatementFileParseException
	 */
	private function error(string $problem) : void{
		throw new GenericStatementFileParseException($problem, $this->lineNo);
	}
}
