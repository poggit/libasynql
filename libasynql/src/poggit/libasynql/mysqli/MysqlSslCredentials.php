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

use JsonSerializable;
use mysqli;

class MysqlSslCredentials implements JsonSerializable{
	/** @var string $key */
	private $key;
	/** @var string $certificate */
	private $certificate;
	/** @var string $caCertificate */
	private $caCertificate;
	/** @var string $caPath */
	private $caPath;
	/** @var string $cipherAlgorithms */
	private $cipherAlgorithms;


	/**
	 * Creates a new {@link MysqlSslCredentials} instance from an array (e.g. from Config), with empty default values.
	 * @param array       $array
	 * @return MysqlSslCredentials
	 */
	public static function fromArray(array $array): MysqlSslCredentials
	{
		return new MysqlSslCredentials(
			$array["key"] ?? "",
			$array["certificate"] ?? "",
			$array["ca-certificate"] ?? "",
			$array["ca-path"] ?? "",
			$array["cipher-algorithms"] ?? ""
		);
	}

	/**
	 * Constructs a new {@link MysqlSslCredentials} by passing parameters directly.
	 *
	 * @param string $key
	 * @param string $certificate
	 * @param string $caCertificate
	 * @param string $caPath
	 * @param string $cipherAlgorithms
	 */
	public function __construct(string $key = "", string $certificate = "", string $caCertificate = "", string $caPath = "", string $cipherAlgorithms = "")
	{
		$this->key = $key;
		$this->certificate = $certificate;
		$this->caCertificate = $caCertificate;
		$this->caPath = $caPath;
		$this->cipherAlgorithms = $cipherAlgorithms;
	}

	/**
	 * Sets the SSL credentials for the given {@link mysqli} instance.
	 *
	 * @param mysqli $mysqli
	 */
	public function applyToInstance(mysqli $mysqli): void
	{
		$mysqli->ssl_set(
			$this->key,
			$this->certificate,
			$this->caCertificate,
			$this->caPath,
			$this->cipherAlgorithms
		);
	}

	public function jsonSerialize(): array
	{
		return [
			"key" => $this->key,
			"certificate" => $this->certificate,
			"caCertificate" => $this->caCertificate,
			"caPath" => $this->caPath,
			"cipherAlgorithms" => $this->cipherAlgorithms
		];
	}
}
