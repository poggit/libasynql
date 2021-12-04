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
use poggit\libasynql\GenericStatement;
use function array_pop;
use function assert;
use function count;
use function fclose;
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
	/** @var string|null */
	private $fileName;
	/** @var resource */
	private $fh;
	/** @var int */
	private $lineNo = 0;

	/** @var string[] */
	private $identifierStack = [];
	/** @var bool */
	private $parsingQuery = false;
	/** @var string[] */
	private $docLines = [];
	/** @var GenericVariable[] */
	private $variables = [];

	/** @var string[] the delimited buffers for the current query */
	private $currentBuffers = [];
	/** @var string[] the lines for the current delimited query buffer */
	private $buffer = [];

	/** @var string|null */
	private $knownDialect = null;
	/** @var GenericStatement[] */
	private $results = [];

	/**
	 * @param string|null $fileName
	 * @param resource    $fh
	 */
	public function __construct(?string $fileName, $fh){
		$this->fileName = $fileName;
		$this->fh = $fh;
	}

	/**
	 * Parses the file, and closes the stream.
	 *
	 * @throws GenericStatementFileParseException if the file contains a syntax error or compile error
	 */
	public function parse() : void{
		try{
			while(($line = fgets($this->fh)) !== false){
				$this->readLine($this->lineNo + 1, $line);
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

	private function readLine(int $lineNo, string $line) : void{
		$this->lineNo = $lineNo; // In fact I don't need this parameter. I just want to get the line number onto the stack trace.
		$line = trim($line);

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
		if($line === ''){
			return true;
		}
		$cmd = $line[0];
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
				$this->dialectCommand($args);
				return true;
			case "{":
				$this->startCommand($args);
				return true;
			case "&":
				$this->delimiterCommand();
				return true;
			case "}":
				$this->endCommand();
				return true;
			case "*":
				$this->docCommand($args, $line, $argOffsets);
				return true;
			case ":":
				$this->varCommand($args, $line, $argOffsets);
				return true;
		}

		return true;
	}

	private function dialectCommand(array $args) : void{
		// dialect command
		if($this->knownDialect !== null){
			$this->error("Dialect declared more than once");
		}

		if(!isset($args[0])){
			$this->error("Missing operand: DIALECT");
		}

		$this->knownDialect = $args[0];
	}

	private function startCommand(array $args) : void{
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
	}

	private function delimiterCommand() : void{
		if(!$this->parsingQuery){
			$this->error("Unexpected &, start a query first");
		}

		if(count($this->buffer) === 0){
			$this->error("Encountered delimiter line without any query content");;
		}

		$this->flushBuffer();
	}

	private function endCommand() : void{
		if(count($this->identifierStack) === 0){
			$this->error("No matching { for }");
		}

		if($this->parsingQuery){
			if(count($this->buffer) === 0){
				$this->error("Documentation/Variables are declared but no query is provided");
			}

			$this->flushBuffer();
			$buffers = $this->currentBuffers;

			$doc = implode("\n", $this->docLines); // double line breaks => single line breaks
			assert($this->knownDialect !== null);
			$stmt = GenericStatementImpl::forDialect($this->knownDialect, implode(".", $this->identifierStack), $buffers, $doc, $this->variables, $this->fileName, $this->lineNo);

			$this->docLines = [];
			$this->variables = [];
			$this->currentBuffers = [];
			$this->buffer = [];
			$this->parsingQuery = false;

			if(isset($this->results[$stmt->getName()])){
				$this->error("Duplicate query name ({$stmt->getName()})");
			}
			$this->results[$stmt->getName()] = $stmt;
		} // end query

		array_pop($this->identifierStack);
	}

	private function flushBuffer() : void {
		$buffer = implode("\n", $this->buffer);
		$this->currentBuffers[] = $buffer;
		$this->buffer = [];
	}

	private function varCommand(array $args, string $line, array $argOffsets) : void{
		if(empty($this->identifierStack)){
			$this->error("Unexpected variable declaration; start a query with { first");
		}

		if(!isset($args[1])){
			$this->error("Missing operand: VAR_TYPE");
		}

		try{
			$var = new GenericVariable($args[0], $args[1], isset($args[2]) ? substr($line, $argOffsets[2] + 1) : null);
		}catch(InvalidArgumentException $e){
			throw $this->error($e->getMessage());
		}
		if(isset($this->variables[$var->getName()])){
			$this->error("Duplicate variable definition of :{$var->getName()}");
		}
		$this->variables[$var->getName()] = $var;
		$this->parsingQuery = true;
	}

	private function docCommand(array $args, string $line, array $argOffsets) : void{
		if(empty($this->identifierStack)){
			$this->error("Unexpected documentation; start a query with { first");
		}

		$this->docLines[] = trim(substr($line, 1));
		$this->parsingQuery = true;
	}

	/**
	 * @param string $problem
	 * @return GenericStatementFileParseException
	 * @throw GenericStatementFileParseException
	 */
	private function error(string $problem) : GenericStatementFileParseException{
		throw new GenericStatementFileParseException($problem, $this->lineNo, $this->fileName);
	}
}
