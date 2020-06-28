# Changelog

## [3.3.1](https://github.com/poggit/libasynql/compare/v3.3.0...v3.3.1)
The next patch version after 3.3.0

### Fixed
- Fixed binary string parameters in SQLite3

## [3.3.0](https://github.com/poggit/libasynql/compare/v3.2.1...v3.3.0)
The next minor version after 3.2.1

### Added
- Usage of pmmp/Snooze instead of usleep to fix high CPU usage
- MySQL pings are now done before a query, if the connection is dead.

## [3.2.1](https://github.com/poggit/libasynql/compare/v3.2.0...v3.2.1)
The next patch version after 3.2.0

### Added
- MySQL threads ping the server every 5 minutes when sleeping

## [3.2.0](https://github.com/poggit/libasynql/compare/v3.1.1...v3.2.0)
The next minor version after 3.1.x

### Added
- Non-prepared dynamic query strings are now possible

### Fixed
- Uses `Thread::wait` instead of `usleep` in slave threads
- Incorrect documentation in executeInsert

## [3.1.1](https://github.com/poggit/libasynql/compare/v3.1.0...v3.1.1)
The next patch version after 3.1.0

### Added
- ExtensionMissingException is more colorful

### Fixed
- Slave connector threads no longer inherit classes from the main thread to reduce memory usage.
  - Only `ini_set` changes and constants (and all builtin functions and classes) are inherited.
  - User classes will be loaded again on slave threads using the class loader.

## [3.1.0](https://github.com/poggit/libasynql/compare/v3.0.0...v3.1.0) (released 2018-07-27 15:49:08 UTC)
Contains minor changes with some externally-usable additions. Targets PocketMine API 3.0.0.

### Added
- Utility class: `CallbackTask`
- Added `DataConnector->waitAll()` to wait for all pending queries to complete. Useful in onEnable() for initializing data.

### Fixed
- Injecting async trace into Error throwables now hacks with the reflections correctly
- Null variables can now be used without triggering "Missing required variable" error

### Updated
- Deprecation of ServerScheduler

## v3.0.0 (released 2018-04-25 15:30:00 UTC)
This is a total rewrite, with an entirely different infrastructure. AsyncTask is no longer used.

### Added
- `DataConnector` as an abstract wrapper for:
  - `GenericStatementFileParser` to load queries from a Prepared Statement File (PSF) into:
    - `GenericStatement` abstraction that formats prepared statements in different dialects
      - Supports MySQL dialect
      - Supports SQLite3 dialect
  - `SqlThreadPool` that manages slave connection threads using the same send/receive queue
    - `SqlSlaveThread` abstraction that connects to a database with different backends
      - Supports mysqli backend
      - Supports SQLite3 backend
- A simple `libasynql::create($plugin, $config, $sqlMap)` method that initializes everything in a single call
- The `libasynql.phar def` tool
