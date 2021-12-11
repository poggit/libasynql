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

namespace poggit\libasynql\psf;

use function ctype_alpha;
use function ctype_digit;
use function strpos;
use function substr;
use poggit\libasynql\psf\ast\PsfArgModifierType;
use poggit\libasynql\psf\ast\PsfArgumentBufferComponent;
use poggit\libasynql\psf\ast\PsfFile;
use poggit\libasynql\psf\ast\PsfGroup;
use poggit\libasynql\psf\ast\PsfLiteralBufferComponent;
use poggit\libasynql\psf\ast\PsfQuery;
use poggit\libasynql\psf\ast\PsfQueryArg;
use poggit\libasynql\psf\ast\PsfQueryBuffer;
use poggit\libasynql\psf\ast\PsfQueryDoc;
use poggit\libasynql\psf\ast\PsfRepetitionIndexBufferComponent;

final class PsfParser {
	private StringStream $reader;

	public function __construct(string $contents, string $debugFileName){
		$this->reader = new StringStream($contents, $debugFileName);
	}

	public function parse() : PsfFile {
		$file = new PsfFile;

		$this->reader->readWhitespace(true);

		while(!$this->reader->isEOF()){
			$this->reader->expect("-- #");

			$this->reader->readWhitespace(false);

			$command = $this->reader->peek(1);
			match($command) {
				"{" => $this->parseBlock($file->getRootGroup()),
				default => throw $this->reader->error("Expect start of block -- #{"),
			};

			$this->reader->readWhitespace(true);
		}

		return $file;
	}

	/**
	 * Parses a group or query block.
	 *
	 * Starts at the { and ends after the }.
	 */
	private function parseBlock(PsfGroup $parent) : void {
		$this->reader->expect("{");
		$this->reader->readWhitespace(false);

		$name = $parent->getPrefix() . $this->reader->readUntilWhitespace();
		$this->reader->readWhitespace(true);

		$parsedBlock = null;
		while($this->reader->matches('/^-- #[  \t]*\{/')) {
			// we are parsing a group

			$parsedBlock = $parsedBlock ?? new PsfGroup($name . ".");

			$this->reader->expect("-- #");
			$this->reader->readWhitespace(false);
			$this->parseBlock($parsedBlock);
		}

		if($parsedBlock === null) {
			$query = new PsfQuery($name);
			$this->parseQuery($query);
			$this->reader->readWhitespace(true);
		} else {
			$this->reader->readWhitespace(true);
			$this->reader->expect("-- #}");
			$parent->addGroup($parsedBlock);
		}
	}

	/**
	 * Parses a query block.
	 *
	 * Starts at the second line and ends after the }.
	 */
	private function parseQuery(PsfQuery $query) : void {
		$buffer = new PsfQueryBuffer;
		$query->addQueryBuffer($buffer);

		while(true) {
			if($this->reader->maybe("-- #")) {
				$this->reader->readWhitespace(false);
				$command = $this->reader->readN(1);

				if($command === ":") {
					$arg = $this->parseArg();
					$query->addArg($arg);
				} elseif($command === "*") {
					$line = $this->reader->readLine();
					$doc = new PsfQueryDoc($line);
					$query->addDoc($doc);
				} elseif($command === "}") {
					break;
				} elseif($command === "&") {
					$buffer = new PsfQueryBuffer;
					$query->addQueryBuffer($buffer);
				} else {
					throw $this->reader->error("Expected : or * or } or &");
				}
			} else {
				// query body
				if($this->reader->maybe('$$')) {
					$body = $this->reader->readPredicate(fn($char) => $char === '$');
					if(($pos = strpos($body, ":")) !== false) {
						$buffer->add(new PsfRepetitionIndexBufferComponent(substr($body, 0, $pos), substr($body, $pos + 1)));
					} else {
						$buffer->add(new PsfRepetitionIndexBufferComponent(null, $body));
					}
					$this->reader->expect('$$');
				} elseif($this->reader->maybe("##")) {
					if($this->reader->maybe("#")) {
						$buffer->add(new PsfRepetitionIndexBufferComponent(null, null));
					} else {
						$body = $this->reader->readPredicate(fn($char) => $char !== "#");
						$buffer->add(new PsfRepetitionIndexBufferComponent($body, null));
						$this->reader->expect("##");
					}
				} elseif($this->reader->maybe(":")) {
					$argument = $this->reader->readPredicate(fn($char) => ctype_alpha($char) || ctype_digit($char) || $char === "_");
					$buffer->add(new PsfArgumentBufferComponent($argument));
				} elseif($this->reader->maybe("\n") || $this->reader->maybe("\r\n")) {
					$buffer->add(new PsfLiteralBufferComponent("\n"));
				} else {
					$text = $this->reader->readN(1); // in case there is a non-escaped # or $
					$text .= $this->reader->readPredicate(fn($char) => strpos("#\$:\r\n", $char) !== false);
					if($text === "") {
						throw $this->reader->error("Unexpected end of file");
					}
				}
			}
		}

		foreach($query->getQueryBuffers() as $buffer) {
		}
	}

	private function parseArg() : PsfQueryArg {
		$name = $this->reader->readUntilWhitespace();
		$this->reader->readWhitespace(false);

		$type = PsfArgModifierType::parse($this->reader->readUntilWhitespace(), $this->reader);
		$this->reader->readWhitespace(false);

		$default = $this->reader->readUntilWhitespace();
		if($default !== "") {
			$default = json_decode($default);
		} else {
			$default = "";
		}

		$this->reader->readWhitespace(true);

		return new PsfQueryArg($name, $type, $default);
	}
}
