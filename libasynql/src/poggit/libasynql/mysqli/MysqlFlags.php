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

namespace poggit\libasynql\mysqli;

/**
 * Result field flags returned by MySQL
 * @see https://github.com/google/mysql/blob/master/include/mysql_com.h#L133
 */
interface MysqlFlags{
	/** Field can't be NULL  */
	public const NOT_NULL_FLAG = 1 << 0;
	/** Field is part of a primary key  */
	public const PRI_KEY_FLAG = 1 << 1;
	/** Field is part of a unique key  */
	public const UNIQUE_KEY_FLAG = 1 << 2;
	/** Field is part of a key  */
	public const MULTIPLE_KEY_FLAG = 1 << 3;
	/** Field is a blob  */
	public const BLOB_FLAG = 1 << 4;
	/** Field is unsigned  */
	public const UNSIGNED_FLAG = 1 << 5;
	/** Field is zerofill  */
	public const ZEROFILL_FLAG = 1 << 6;
	/** Field is binary    */
	public const BINARY_FLAG = 1 << 7;

	/* The following are only sent to new clients */
	/** field is an enum  */
	public const ENUM_FLAG = 1 << 8;
	/** field is a autoincrement field  */
	public const AUTO_INCREMENT_FLAG = 1 << 9;
	/** Field is a timestamp  */
	public const TIMESTAMP_FLAG = 1 << 10;
	/** field is a set  */
	public const SET_FLAG = 1 << 11;
	/** Field doesn't have default value  */
	public const NO_DEFAULT_VALUE_FLAG = 1 << 12;
	/** Field is set to NOW on UPDATE  */
	public const ON_UPDATE_NOW_FLAG = 1 << 13;
	/** Field is num (for clients)  */
	public const NUM_FLAG = 1 << 15;

	/** Used to get fields in item tree */
	public const GET_FIXED_FIELDS_FLAG = 1 << 18;
	/** Field part of partition func */
	public const FIELD_IN_PART_FUNC_FLAG = 1 << 19;
}
