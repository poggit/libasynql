# libasynql [![Poggit-CI](https://poggit.pmmp.io/ci.badge/poggit/libasynql/libasynql)](https://poggit.pmmp.io/ci/poggit/libasynql/libasynql)
Asynchronous MySQL access library for PocketMine plugins.

libasynql provides the ability to execute MySQL queries through asynchronous tasks from the PocketMine API. `callable` objects can be passed to execute actions after the query is executed and returned to the main thread.

## Initialization
Plugins using libasynql should call `\libasynql\PingMysqlTask::init()` during startup if asynchronous MySQLi connections are to be created.

For example, it should have a line like this:

```php
\libasynql\PingMysqlTask::init($this, $credentials);`
```

where `$this` refers to the plugin main class and `$credentials` is the [`MysqlCredentials`](#mysqlcredentials) representing the connection credentials.

## Finalization
It is a good practice to close mysqli connections in async workers before the plugin gets disabled. To do so, all `\libasynql\ClearMysqlTask::closeAll()` in `onDisable()` of your plugin.

For example:

```php
\libasynql\PingMysqlTask::close($this, $credentials);`
```

## Doxygen docs
Visit https://poggit.github.io/libasynql for docs.

## MysqlCredentials
MySQL login parameters are passed with the `MysqlCredentials` class. Developers can have something like this in their `config.yml`:

```yaml
mysql:
  host: 127.0.0.1
  username: root
  password: ""
  schema: "schema_name"
  port: 3306
  socket: ""
```

A `MysqlCredentials` instance can be directly created from this array:

```php
$credentials = MysqlCredentials::fromArray($this->getConfig()->get("mysql"));
```

Apart from `schema`, all other attributes are optional, and their default values are as shown in the YAML snippet above.

## Pool
Pool MySQL Access provides MySQL access through PocketMine's AsyncTask API. 

### `QueryMysqlTask::getMysqli`
This is the superclass for all pool query AsyncTask classes. It provides a `getMysqli` method, which caches the `mysqli` instance in a worker thread local storage.

In simple words, each `mysqli` instance can only be used in one thread, but the PocketMine AsyncTask API executes the AsyncTasks in different worker threads. Therefore, the AsyncTask thread store is used to store a `mysqli` instance per thread.

### `DirectQueryMysqlTask`, the direct implementation
This class is instantiable, so you can execute queries with it directly.

```php
public function DirectQueryMysqlTask::__construct(MysqlCredentials $credentials, string $query, array $args = [], callable $callback = null);
```

The first parameter is the `MysqlCredentials` used to instantiate a new `mysqli` connection, if this worker thread has never handled any MySQL tasks yet. **Always** pass a valid value for this parameter even if you have already executed a MySQL query before.

The second parameter is the query string to execute. It can contain `?` for parameters, which are bound with the third parameter, `$args`. Refer to [PHP documentation for `mysqli_stmt::bind_param`](http://php.net/mysqli-stmt.bind-param) for reference.

The third parameter is an array of parameter entries. Each parameter entry must be an array of two elements, the first one the type (`i` for integer, `d` for floats or `s` for strings) (libasynql does not have proper support for blobs yet) and the second one the value. **Only pass primitive values for the value**, i.e. only strings and numbers. An example of `$args` should look like this:

```php
[
    ["i", 0xF00BA4],
    ["s", "foobar"]
]
```

The fourth parameter is optional. Passing null or void would cause nothing to be executed after task completion, except that warning will be raised upon errors in the query. Otherwise, it should be a `callable` (i.e. a `Closure` anonymous function, a function name or an array of object and method name). 

> **Warning**: Strong reference to the `callable` object will be retained until the task completes, according to the PocketMine `AsyncTask::fetchLocal` API. Hence, any objects in the `callable`, or the `$this` context that defined the callable as an anonymous function, as well as any variables `use`d by the anonymous function, will remain strongly referenced until the task completes. Therefore, creating a `DirectQueryMysqlTask` with callable may result in delay of garbage collection, or even memory leak if the AsyncTask runs for a long time (very unlikely with DirectQueryMysqlTask though, unless you have a really very slow query that takes minutes or hours to execute).

This callable can accept up to one parameter, which shall receive a `MysqlResult` instance representing the outcome of the query. See the classes in the `libasynql\result` for more details.

### MysqlSelectResult fixing
PHP's `mysqli` API returns some values as strings or null for some values. The `MysqlSelectResult::fixTypes` method can be used to correct the types in the result rows.
