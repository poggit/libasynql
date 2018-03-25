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

class GenericStatementFileParseException extends InvalidArgumentException{
	private $problem;
	private $lineNo;
	private $queryFile;

	public function __construct(string $problem, int $lineNo, string $file = null){
		$this->problem = $problem;
		$this->lineNo = $lineNo;
		$this->queryFile = $file ?? "SQL file";

		parent::__construct("Error parsing prepared statement file: $problem on line $lineNo in $file");
	}

	public function getProblem() : string{
		return $this->problem;
	}

	public function getLineNo() : int{
		return $this->lineNo;
	}

	public function getQueryFile() : string{
		return $this->queryFile;
	}
}
