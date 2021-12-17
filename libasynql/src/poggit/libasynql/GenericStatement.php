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

use poggit\libasynql\generic\GenericVariable;

interface GenericStatement{
	/**
	 * Returns the dialect this query is intended for.
	 *
	 * @return string one of the constants in {@link SqlDialect}
	 */
	public function getDialect() : string;

	/**
	 * Returns the identifier name of this query
	 *
	 * @return string[]
	 */
	public function getName() : string;

	public function getQuery() : array;

	public function getDoc() : string;

	/**
	 * The variable list ordered by original declaration order
	 *
	 * @return GenericVariable[]
	 */
	public function getOrderedVariables() : array;

	/**
	 * Returns the variables required by this statement
	 *
	 * @return GenericVariable[]
	 */
	public function getVariables() : array;

	public function getFile() : ?string;

	public function getLineNumber() : int;

	/**
	 * Creates a query based on the args and the backend
	 *
	 * @param mixed[]     $vars        the input arguments
	 * @param string|null $placeHolder the backend-dependent variable placeholder constant, if any
	 * @param mixed[][]   &$outArgs    will be filled with the variables to be passed to the backend
	 * @return string[]
	 */
	public function format(array $vars, ?string $placeHolder, ?array &$outArgs) : array;
}
