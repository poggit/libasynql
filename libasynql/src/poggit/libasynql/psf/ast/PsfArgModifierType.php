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

namespace poggit\libasynql\psf\ast;

use poggit\libasynql\psf\StringStream;

use function strpos;

final class PsfArgModifierType implements PsfArgType {
	public const MODIFIER_LIST = "list:";
	public const MODIFIER_OPTIONAL = "optional:";

	public function __construct(
		private string $modifier,
		private PsfArgType $inner,
	) {}

	public function getModifier(): string {
		return $this->modifier;
	}

	public function getInner(): PsfArgType {
		return $this->inner;
	}

	public static function parse(string $type, StringStream $stream) : PsfArgType {
		if(strpos($type, self::MODIFIER_LIST) === 0) {
			return new self(self::MODIFIER_LIST, self::parse(substr($type, strlen(self::MODIFIER_LIST)), $stream));
		}
		if(strpos($type, self::MODIFIER_OPTIONAL) === 0) {
			return new self(self::MODIFIER_OPTIONAL, self::parse(substr($type, strlen(self::MODIFIER_OPTIONAL)), $stream));
		}

		if($type !== PsfArgScalarType::SCALAR_STRING && $type !== PsfArgScalarType::SCALAR_INT && $type !== PsfArgScalarType::SCALAR_FLOAT && $type !== PsfArgScalarType::SCALAR_BOOL) {
			throw $stream->error("Invalid type $type");
		}
		return new PsfArgScalarType($type);
	}
}
