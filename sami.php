<?php

declare(strict_types=1);

return new Sami\Sami(__DIR__ . "/libasynql/src", [
	"title" => "libasynql 3.0.0",
	"build_dir" => __DIR__ . "/docs/sami",
	"cache_dir" => __DIR__ . "/.sami/cache",
	"remote_repository" => new Sami\RemoteRepository\GitHubRemoteRepository("poggit/libasynql", __DIR__),
]);
