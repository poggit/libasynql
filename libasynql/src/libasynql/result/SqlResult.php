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
 * Represents a successful or error result from the database.
 */
abstract class SqlResult{
	/** @var float */
	private $timing;

	public function setTiming(float $timing) : SqlResult{
		$this->timing = $timing;
		return $this;
	}

	public function getTiming() : float{
		return $this->timing;
	}

	public function assertSelect() : SqlSelectResult{
		if($this instanceof SqlSelectResult){
			return $this;
		}else{
			assert($this instanceof SqlErrorResult);
			/** @var SqlErrorResult $this */
			throw $this->throw();
		}
	}
}
