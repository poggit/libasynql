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

use Closure;
use function strlen;
use function strrpos;
use function substr;

final class StringStream {
	private int $index = 0;
	private int $lineNo = 0;
	private int $colNo = 0;

	public function __construct(
		private string $buffer,
		private string $debugFileName,
	) {}

	/**
	 * @param Closure(string): bool $predicate
	 */
	public function readPredicate(Closure $predicate) : string {
		$index = $this->index;

		while($index < strlen($this->buffer)) {
			if($predicate($this->buffer[$index])) {
				$index++;
			} else {
				break;
			}
		}

		return $this->readN($index - $this->index);
	}

	public function readN(int $n) : string {
		$ret = substr($this->buffer, $this->index, $n);

		$this->lineNo += substr_count($ret, "\n");

		$lastLf = strrpos($ret, "\n");
		if($lastLf === false) {
			$this->colNo += strlen($ret);
		} else {
			$this->colNo = strlen($ret) - $lastLf;
		}

		return $ret;
	}

	public function readLine() : string {
		$line = $this->readPredicate(fn($char) => !($char === "\r" || $char === "\n"));
		$this->readWhitespace(true);
		return $line;
	}

	public function readWhitespace(bool $newline) : string {
		return $this->readPredicate(fn($char) => $char === " " || $char === "\t" || ($newline && ($char === "\r" || $char === "\n")));
	}

	public function readUntilWhitespace() : string {
		$ret = $this->readPredicate(fn($char) => !($char === " " || $char === "\t" || $char === "\r" || $char === "\n"));
		if($ret === "") {
			throw $this->error("Expected non-whitespace");
		}
		return $ret;
	}

	public function matches(string $regex) : bool {
		return preg_match($regex, substr($this->buffer, $this->index)) > 0;
	}

	public function peek(int $size) : string {
		return substr($this->buffer, $this->index, min($size, strlen($this->buffer) - $this->index));
	}

	public function expect(string $string) : void {
		if($this->peek(strlen($string)) !== $string) {
			throw $this->error("Expected \"$string\"");
		}

		$this->readN(strlen($string));
	}

	public function maybe(string $string) : bool {
		if($this->peek(strlen($string)) !== $string) {
			return false;
		}

		$this->readN(strlen($string));
		return true;
	}

	public function error(string $message) : ParseException {
		throw new ParseException("$message in $this->debugFileName on line $this->lineNo:$this->colNo");
	}

	public function isEOF() : bool {
		return $this->index >= strlen($this->buffer);
	}
}
