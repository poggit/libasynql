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

use InvalidArgumentException;
use Generator;
use Logger;
use poggit\libasynql\generic\GenericStatementFileParseException;
use poggit\libasynql\result\SqlColumnInfo;
use SOFe\AwaitGenerator\Await;

/**
 * Represents a database connection or a group of database connections
 */
interface DataConnector{
	/**
	 * If true, logger is set to the plugin logger. If false, queries are not logged.
	 *
	 * @param bool $loggingQueries
	 */
	public function setLoggingQueries(bool $loggingQueries) : void;

	/**
	 * Returns whether the logger is not null, i.e. queries are being logged.
	 *
	 * @return bool
	 */
	public function isLoggingQueries() : bool;

	/**
	 * Sets the logger used to log queries, or null to not log queries
	 *
	 * @param Logger|null $logger
	 */
	public function setLogger(?Logger $logger) : void;

	/**
	 * Returns the logger used to log queries, or null if not logging queries
	 *
	 * @return Logger|null
	 */
	public function getLogger() : ?Logger;

	/**
	 * Loads pre-formatted queries from a readable stream resource.
	 *
	 * The implementation will close the stream after reading.
	 *
	 * @param resource    $fh       a stream that supports <code>feof()</code>, <code>fgets()</code> and <code>fclose()</code>.
	 * @param string|null $fileName the filename providing the stream, only used for debugging and documentation purposes
	 *
	 * @throws GenericStatementFileParseException if the file contains a syntax error or compile error
	 * @throws InvalidArgumentException if the file introduces statements that duplicate the names of those previously loaded
	 */
	public function loadQueryFile($fh, string $fileName = null) : void;

	/**
	 * Loads a pre-formatted query.
	 *
	 * @param GenericStatement $stmt
	 *
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
	public function executeGeneric(string $queryName, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void;

	/**
	 * Executes a generic query that either succeeds or fails.
	 *
	 * This is the await-generator variant. Non await-generator users should not use this function.
	 *
	 * @param string  $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[] $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @return Generator<mixed, Await::RESOLVE|Await::REJECT, mixed, null>
	 */
	public function asyncGeneric(string $queryName, array $args = []) : Generator;

	/**
	 * Executes a query that changes data.
	 *
	 * If multiple delimited queries exist in the query, they will be executed in order, but only the last result will be returned.
	 * The last statement must be a change query.
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSuccess an optional callback when the query has succeeded: <code>function(int $affectedRows) : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeChange(string $queryName, array $args = [], ?callable $onSuccess = null, ?callable $onError = null) : void;

	/**
	 * Executes a query that changes data.
	 *
	 * This function is the await-generator variant. Non await-generator users should not use this function.
	 *
	 * The generator returns the number of affected rows.
	 *
	 * @param string  $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[] $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @return Generator<mixed, Await::RESOLVE|Await::REJECT, mixed, int>
	 */
	public function asyncChange(string $queryName, array $args = []) : Generator;

	/**
	 * Executes an insert query that results in an insert ID.
	 *
	 * If multiple delimited queries exist in the query, they will be executed in order, but only the last result will be returned.
	 * The last statement must be an insert query.
	 *
	 * @param string        $queryName  the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args       the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onInserted an optional callback when the query has succeeded: <code>function(int $insertId, int $affectedRows) : void{}</code>
	 * @param callable|null $onError    an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeInsert(string $queryName, array $args = [], ?callable $onInserted = null, ?callable $onError = null) : void;

	/**
	 * Executes an insert query that results in an insert ID.
	 *
	 * This function is the await-generator variant. Non await-generator users should not use this function.
	 *
	 * The generator returns a two-element array.
	 * The first element is the insert ID.
	 * The second element is the number of rows affected.
	 *
	 * Example usage:
	 *
	 * ```
	 * [$insertId, $affectedRows] = yield $connector->asyncChange(Queries::QUERY_NAME, $some, $arguments);
	 * ```
	 *
	 * @param string  $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[] $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @return Generator<mixed, Await::RESOLVE|Await::REJECT, mixed, array{int, int}>
	 */
	public function asyncInsert(string $queryName, array $args = []) : Generator;

	/**
	 * Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL's <code>SHOW TABLES</code> query are also allowed.
	 *
	 * If multiple delimited queries exist in the query, they will be executed in order, but only the last result will be returned.
	 * The last statement must be a select query (or e.g. <code>SHOW TABLES</code> queries).
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSelect  an optional callback when the query has succeeded: <code>function(array[] $rows, SqlColumnInfo[] $columns) : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeSelect(string $queryName, array $args = [], ?callable $onSelect = null, ?callable $onError = null) : void;

	/**
	 * Executes a query with probably multiple delimited queries, and returns an array of {@link SqlResult}s mapping to each query.
	 *
	 * @param string        $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[]       $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @param callable|null $onSelect  an optional callback when the query has succeeded: <code>function(SqlResult[] $results) : void{}</code>
	 * @param callable|null $onError   an optional callback when the query has failed: <code>function({@link SqlError} $error) : void{}</code>
	 */
	public function executeMulti(string $queryName, array $args, int $mode, ?callable $handler = null, ?callable $onError = null) : void;

	/**
	 * @param string[] $queries
	 * @param mixed[][] $args
	 * @param int[] $modes
	 */
	public function executeImplRaw(array $queries, array $args, array $modes, callable $handler, ?callable $onError) : void;

	/**
	 * Executes a select query that returns an SQL result set. This does not strictly need to be SELECT queries -- reflection queries like MySQL's <code>SHOW TABLES</code> query are also allowed.
	 *
	 * This function is the await-generator variant. Non await-generator users should not use this function.
	 *
	 * The generator returns the number of affected rows.
	 *
	 * If {@link SqlColumnInfo} is needed, use `asyncSelectWithInfo` instead.
	 *
	 * @param string  $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[] $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @return Generator<mixed, Await::RESOLVE|Await::REJECT, mixed, array[] $rows>
	 */
	public function asyncSelect(string $queryName, array $args = []) : Generator;

	/**
	 * A variant of {@link #asyncSelect} with different return value.
	 *
	 * The generator returns a two-element array.
	 * The first element is the array of rows.
	 * The second element is an array of \link{SqlColumnInfo} objects.
	 *
	 * Example usage:
	 *
	 * ```
	 * [$rows, $info] = yield $connector->asyncSelectWithInfo(Queries::QUERY_NAME, $some, $arguments);
	 * ```
	 *
	 * @param string  $queryName the {@link GenericPreparedStatement} query name
	 * @param mixed[] $args      the variables as defined in the {@link GenericPreparedStatement}
	 * @return Generator<mixed, Await::RESOLVE|Await::REJECT, mixed, array{array[], SqlColumnInfo[]}>
	 */
	public function asyncSelectWithInfo(string $queryName, array $args = []) : Generator;

	/**
	 * This function waits all pending queries to complete then returns. This is as if the queries were executed in blocking mode (not async).
	 *
	 * This method should only under very rare events like server start/stop. This should not be run trivially (e.g. every time player joins), because otherwise this is not async.
	 */
	public function waitAll() : void;

	/**
	 * Closes the connection and/or all child connections. Remember to call this method when the plugin is disabled or the data provider is switched.
	 */
	public function close() : void;
}
