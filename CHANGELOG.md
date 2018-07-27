# Changelog

## [Unreleased (v3.1.0)](https://github.com/poggit/libasynql/compare/v3.0.0...HEAD)
Contains minor changes with some externally-usable additions. Supports PocketMine 3.0.0.

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
