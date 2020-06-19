# libasynql v3.3.0 <img src="https://poggit.pmmp.io/ci.badge/poggit/libasynql/libasynql" align="right"/>
Asynchronous SQL access library for PocketMine plugins.

## Usage
The basic use of libasynql has 5 steps:
1. Add default database settings in your `config.yml`.
2. Write down all the SQL queries you will use in a resource file
3. Initialize the database in `onEnable()`.
4. Finalize the database in `onDisable()`.
5. Obviously, and most importantly, use libasynql in your code.

### Configuration
To let the user choose what database to use, copy the following into your default `config.yml`. Remember to change the default schema name under `mysql`.

```yaml
database:
  # The database type. "sqlite" and "mysql" are supported.
  type: sqlite

  # Edit these settings only if you choose "sqlite".
  sqlite:
    # The file name of the database in the plugin data folder.
    # You can also put an absolute path here.
    file: data.sqlite
  # Edit these settings only if you choose "mysql".
  mysql:
    host: 127.0.0.1
    # Avoid using the "root" user for security reasons.
    username: root
    password: ""
    schema: your_schema
  # The maximum number of simultaneous SQL queries
  # Recommended: 1 for sqlite, 2 for MySQL. You may want to further increase this value if your MySQL connection is very slow.
  worker-limit: 1
```

### Initialization and Finalization
libasynql simplifies the process of initializing a database into a single function call.

```php
use pocketmine\plugin\PluginBase;
use poggit\libasynql\libasynql;

class Main extends PluginBase{
    private $database;

    public function onEnable(){
        $this->saveDefaultConfig();
        $this->database = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);
    }

    public function onDisable(){
        if(isset($this->database)) $this->database->close();
    }
}
```

The [`\poggit\libasynql\libasynql::create()`](https://poggit.github.io/libasynql/doxygen/classpoggit_1_1libasynql_1_1libasynql.html#add1449f8fef87cc275a3d99f2440c642) method accepts 3 parameters:
- Your plugin main
- The config entry where the database settings should be found
- An array for your SQL files. For each SQL dialect you are supporting, use it as the key, and use the path (or array of paths, relative to the `resources` folder) of the SQL files as the value. We are going to create them in the next step.

It returns a [`\poggit\libasynql\DataConnector`](https://poggit.github.io/libasynql/doxygen/interfacepoggit_1_1libasynql_1_1_data_connector.html) object, which is the main query interface.

In case of error, a ConfigException or an SqlError will be thrown. If not caught by the plugin, this will go straight out of onEnable() and disable the plugin. Therefore, make sure to check `isset($this->database)` before calling `$this->database->close()` in onDisable().

### Creating SQL files
In the resources file, create one file for each SQL dialect you are supporting, e.g. `resources/sqlite.sql` and `resources/mysql.sql`.

Write down all the queries you are going to use in each file, using the [Prepared Statement File format](#prepared-statement-file-format).

### Calling libasynql functions
Finally, we are prepared to use libasynql in code!

There are 4 query modes you can ues: GENERIC, CHANGE, INSERT and SELECT.
- GENERIC: You don't want to know anything about the query except whether it is successful. You may want to use this in `CREATE TABLE` statements.
- CHANGE: Your query modifies the database, and you want to know how many rows are changed. Useful in `UPDATE`/`DELETE` statements.
- INSERT: Your query is an `INSERT INTO` query for a table with an `AUTO_INCREMENT` key. You will receive the auto-incremented row ID.
- SELECT: Your query expects a result set, e.g. a `SELECT` statement, or reflection queries like `EXPLAIN` and `SHOW TABLES`. You will receive a `SqlSelectResult` object that represents the columns and rows returned.

They have their respective methods in DataConnector: `executeGeneric`, `executeChange`, `executeInsert`, `executeSelect`. They require the same parameters:

- The name of the prepared statement
- The variables for the query, in the form of an associative array "variable name (without colon)" => value
- An optional callable triggered if the query succeeded, accepting different arguments:
  - GENERIC: no arguments
  - CHANGE: `function(int $affectedRows)`
  - INSERT: `function(int $insertId, int $affectedRows)`
  - SELECT: `function(array $rows)`
- An optional callable triggered if an error occurred. Can accept an `SqlError` object.

## Prepared Statement File Format
A Prepared Statement File (PSF) contains the queries that a plugin uses. The content is valid SQL, so it is OK to edit with a normal SQL editor.

The PSF is annotated by "command lines", which start with `-- #`, followed by the command symbol, then the arguments. Between the `#` and the command symbol, there can be zero to infinite spaces or tabs; between the command symbol and the arguments, there can also be zero to infinite spaces or tabs. Between every two arguments, one to infinite spaces or tabs are required.

### Dialect declaration
A PSF always starts with a dialect declaration.

#### Symbol
`!`

#### Arguments
##### DIALECT
Possible values: `mysql`, `sqlite`

#### Example

```sql
-- #! mysql
```

### Group declaration
Queries may be organized by groups. Each group has an identifier name, and a group can be stacked under another. Groups and queries under a group will be prepended the parent group's identifier plus a period in their own identifiers.

For example, if a parent group declares an identifier `foo`, and the child group/query declares an identifier `bar`, the real identifier for the child group/query is `foo.bar`.

Duplicate group identifier declarations are allowed, as long as the resultant queries do not have identical full identifiers.

#### Symbol
- Start: `{`
- End: `}`

#### Arguments (Start)
##### IDENTIFIER_NAME
The name of this group.

All characters except spaces and tabs are allowed, including periods.

#### Example

```sql
-- #{ group.name.here
	-- #{ child.name
		-- the identifier of the child group is "group.name.here.child.name"
	-- #}
-- #}
```

Note that PSF is insensitive about spaces and tabs, so this variant is equivalent:

```sql
-- #{ group.name.here
-- #    { child.name
		-- the identifier of the child group is still "group.name.here.child.name"
-- #    }
-- #}
```

### Query declaration
A query is declared like a group. A query does not need to belong to a group, because the query can declare the periods in its own identifier, which has equivalent effect as groups.

Child groups are not allowed in a query declaration. In other words, a `{}` pair either has other group/query declarations inside, or has query text (and optionally variable declarations) inside. It cannot have both.

#### Symbol
- Start: `{` (same as group declaration)
- End: `}`

#### Arguments
Same arguments as a group declaration.

### Variable declaration
A variable declaration declares the required and optional variables for this query. It is only allowed inside a query declaration.

#### Symbol
- `:`

### Nullable variable declaration
Declare if the variable can accepts `null` values. It is only allowed inside a query declaration.

#### Symbol
- `?`

#### Arguments
##### VAR_NAME
The name of the variable. Any characters apart from spaces, tabs and colons are allowed. However, to comply with ordinary SQL editors, using "normal" symbols (e.g. variable names in other programming languages) is recommended.

##### VAR_TYPE
The variable type. Possible values:
- `string`
- `int`
- `float`
- `bool`
- `timestamp`
- `list`

##### VAR_DEFAULT
If the variable is optional, it declares a default value.

This argument is not affected by spaces. It starts from the first non-space non-tab character after VAR_TYPE, and ends before the trailing space/tab characters of the line

###### `string` default
There are two modes, literal string and JSON string.

If the argument starts with a `"` and ends with a `"`, the whole argument will be parsed in JSON. Otherwise, the whole string is taken literally.

###### `int` default
A numeric value that can be parsed by [`(int)` cast, equivalent to `intval`](https://php.net/intval).

###### `float` default
A numeric value that can be parsed by [`(float)` cast, equivalent to `floatval`](https://php.net/floatval).

###### `bool` default
`true`, `on`, `yes` or `1` will result in true. Other values, as long as there is something, will result default false. (If there is nothing, the variable will not be optional)

###### `timestamp` default
A numeric value that can be parsed by [`(int)` cast, equivalent to `intval`](https://php.net/intval) or [`(float)` cast, equivalent to `floatval`](https://php.net/floatval)

**Allowed default values:**
- `0`
- `NOW`

###### `list`
An array of values whose type must be specified in the same way as the variable declaration.

**Default value is not allowed**

Example:
```sql
-- #{ group.name
-- #    :var_name1 list:int
-- #    :var_name2 list?float
-- #}
```

### Query text
Query text is not a command, but the non-commented part between the start and end commands of a query declaration.

Variables are interpolated in query text using the `:var` format. Note that libasynql uses a homebrew algorithm for identifying the variable positions, so they might be inaccurate.

## Featured examples
- [cucumber](https://github.com/adeynes/cucumber)
- [BlockPets](https://github.com/BlockHorizons/BlockPets/blob/4163b4f402494e7ec71b0911c413b8f199904b0e/src/BlockHorizons/BlockPets/pets/datastorage/SQLDataStorer.php)
