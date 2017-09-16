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

/**
 * Represents a successful result of a MySQL query.
 */
class SqlSuccessResult extends SqlResult{
	/**
	 * The number of rows affected in this query. May return unexpected values.
	 *
	 * @see <a href="https://php.net/mysqli.affected-rows">mysqli::$affected_rows</a>
	 *
	 * @var int $affectedRows
	 */
	public $affectedRows;
	/**
	 * The last insert ID returned from the database. <b>May be irrelevant to the query of this result.</b>
	 *
	 * @see <a href="https://php.net/mysqli.insert-id">mysqli::$insert_id</a>
	 * @var int $insertId
	 */
	public $insertId;

	/**
	 * Creates a {@link MysqlSelectResult} and copies own contents into it.
	 *
	 * @internal Only intended for internal use.
	 *
	 * @return SqlSelectResult
	 */
	public function asSelectResult() : SqlSelectResult{
		$result = new SqlSelectResult();
		$result->affectedRows = $this->affectedRows;
		$result->insertId = $this->insertId;
		return $result;
	}
}
