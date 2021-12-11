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

final class PsfQuery {
	/** @var list<PsfQueryDoc> */
	private array $docs = [];
	/** @var list<PsfQueryArg> */
	private array $args = [];
	/** @var list<PsfQueryBuffer> */
	private array $queryBuffers = [];

	public function __construct(
		private string $name,
	) {}

	/**
	 * @return list<PsfQueryDoc>
	 */
	public function getDocs(): array {
		return $this->docs;
	}

	/**
	 * @return list<PsfQueryArg>
	 */
	public function getArgs(): array {
		return $this->args;
	}

	/**
	 * @return list<PsfQueryBuffer>
	 */
	public function getQueryBuffers(): array {
		return $this->queryBuffers;
	}

	public function getName(): string {
		return $this->name;
	}

	public function addDoc(PsfQueryDoc $doc): void {
		$this->docs[] = $doc;
	}

	public function addArg(PsfQueryArg $arg): void {
		$this->args[] = $arg;
	}

	public function addQueryBuffer(PsfQueryBuffer $queryBuffer): void {
		$this->queryBuffers[] = $queryBuffer;
	}
}
