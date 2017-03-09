<?php

/*
 * libasynql
 *
 * Copyright (C) 2016 Poggit
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

namespace libasynql\result;

use libasynql\exception\MysqlException;

/**
 * MysqlResult when an error occurred during the query
 */
class MysqlErrorResult extends MysqlResult{
	/** @var string $exception Serialized form of the {@link MysqlException} object. */
	private $exception;

	public function __construct(MysqlException $ex){
		$this->setException($ex);
	}

	/**
	 * @param MysqlException $exception
	 */
	public function setException(MysqlException $exception){
		$this->exception = serialize($exception);
	}

	/**
	 * @return MysqlException
	 */
	public function getException() : MysqlException{
		return unserialize($this->exception);
	}
}
