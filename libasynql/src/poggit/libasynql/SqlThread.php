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

interface SqlThread{
	public const MODE_GENERIC = 0;
	public const MODE_CHANGE = 1;
	public const MODE_INSERT = 2;
	public const MODE_SELECT = 3;

	public function start();

	public function join();

	public function isReallyRunning() : bool;

	public function stopRunning() : void;

	public function addQuery(int $queryId, int $mode, string $query, array $params) : void;

	public function readResults(array &$callbacks) : void;

	public function connCreated() : bool;

	public function hasConnError() : bool;

	public function getConnError() : ?string;
}
