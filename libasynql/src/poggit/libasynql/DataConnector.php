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

use InvalidArgumentException;
use poggit\libasynql\generic\GenericStatementFileParseException;

/**
 * Represents a database connection or a group of database connections
 */
interface DataConnector{
	/**
	 * Loads pre-formatted queries from a readable stream resource.
	 *
	 * The implementation will close the stream after reading.
	 *
	 * @param resource $fh a stream that supports <code>feof()</code>, <code>fgets()</code> and <code>fclose()</code>.
	 *
	 * @throws GenericStatementFileParseException if the file contains a syntax error or compile error
	 * @throws InvalidArgumentException if the file introduces statements that duplicate the names of those previously loaded
	 */
	public function loadQueryFile($fh) : void;

	/**
	 * Loads a pre-formatted query.
	 *
	 * @param GenericStatement $stmt
	 * @throws InvalidArgumentException if the statement duplicates the name of one previously loaded
	 */
	public function loadQuery(GenericStatement $stmt) : void;

	/**
	 * Executes a generic query that either succeeds or fails.
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSuccess an optional callback when the query has succeeded: <code>function() : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeGeneric(string $queryName, array $args, ?callable $onSuccess = null, ?callable $onError = null) : void;

	/**
	 * Executes a query that changes data.
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSuccess an optional callback when the query has succeeded: <code>function(int $affectedRows) : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeChange(string $queryName, array $args, ?callable $onSuccess = null, ?callable $onError = null) : void;

	/**
	 * Executes an insert query that results in an insert ID.
	 *
	 * @param string        $queryName  the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args       the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onInserted an optional callback when the query has succeeded: <code>function(int $insertId, callable $affectedRows) : void{}</code>
	 * @param callable|null $onError    an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeInsert(string $queryName, array $args, ?callable $onInserted = null, ?callable $onError = null) : void;

	/**
	 * Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL's <code>SHOW TABLES</code> query are also allowed.
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSelect  an optional callback when the query has succeeded: <code>function({@link \poggit\libasynql\result\SqlSelectResult SqlSelectResult} $result) : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeSelect(string $queryName, array $args, ?callable $onSelect = null, ?callable $onError = null) : void;

	/**
	 * Closes the connection and/or all child connections. Remember to call this method when the plugin is disabled or the data provider is switched.
	 */
	public function close() : void;
}
