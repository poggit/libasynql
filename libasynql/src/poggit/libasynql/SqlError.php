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

namespace poggit\libasynql;

use Exception;

/**
 * Represents a generic error when executing a SQL statement.
 */
class SqlError extends Exception{
	/**
	 * Returned by {@link SqlError::getStage() getStage()}, indicating that an error occurred while connecting to the database
	 */
	public const STAGE_CONNECT = "CONNECT";
	/**
	 * Returned by {@link SqlError::getStage() getStage()}, indicating that an error occurred while preparing the query
	 */
	public const STAGE_PREPARE = "PREPARE";
	/**
	 * Returned by {@link SqlError::getStage() getStage()}, indicating that an error occurred while the SQL backend is executing the query
	 */
	public const STAGE_EXECUTE = "EXECUTION";
	/**
	 * Returned by {@link SqlError::getStage() getStage()}, indicating that an error occurred while handling the response of the query
	 */
	public const STAGE_RESPONSE = "RESPONSE";

	private $stage;
	private $errorMessage;
	private $query;
	private $args;

	public function __construct(string $stage, string $errorMessage, string $query = null, array $args = null){
		$this->stage = $stage;
		$this->errorMessage = $errorMessage;
		$this->query = $query;
		$this->args = $args;

		parent::__construct("SQL $stage error: $errorMessage");
	}

	/**
	 * Returns the stage of query execution at which the error occurred.
	 *
	 * @return string
	 */
	public function getStage() : string{
		return $this->stage;
	}

	/**
	 * Returns the error message
	 *
	 * @return string
	 */
	public function getErrorMessage() : string{
		return $this->errorMessage;
	}

	/**
	 * Returns the original query
	 *
	 * @return string|null
	 */
	public function getQuery() : ?string{
		return $this->query;
	}

	/**
	 * Returns the original arguments passed to the query
	 *
	 * @return mixed[]|null
	 */
	public function getArgs() : ?array{
		return $this->args;
	}
}
