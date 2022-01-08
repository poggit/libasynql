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

interface SqlThread{
	public const MODE_GENERIC = 0;
	public const MODE_CHANGE = 1;
	public const MODE_INSERT = 2;
	public const MODE_SELECT = 3;

	/**
	 * Joins the thread
	 *
	 * @see https://php.net/thread.join Thread::join
	 */
	public function join();

	/**
	 * Signals the thread to stop waiting for queries when the send buffer is cleared.
	 */
	public function stopRunning() : void;

	/**
	 * Adds a query to the queue.
	 *
	 * @param int      $queryId
	 * @param int      $mode
	 * @param string[] $query
	 * @param mixed[]  $params
	 */
	public function addQuery(int $queryId, array $modes, array $queries, array $params) : void;

	/**
	 * Handles the results that this query has completed
	 *
	 * @param callable[] $callbacks
	 */
	public function readResults(array &$callbacks) : void;

	/**
	 * Checks if the initial connection has been made, no matter successful or not.
	 *
	 * @return bool
	 */
	public function connCreated() : bool;

	/**
	 * Checks if the initial connection failed.
	 *
	 * @return bool
	 */
	public function hasConnError() : bool;

	/**
	 * Gets the error of the initial connection.
	 *
	 * @return null|string
	 */
	public function getConnError() : ?string;
}
