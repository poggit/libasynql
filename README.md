# libasynql [![Poggit-CI](https://poggit.pmmp.io/ci.badge/poggit/libasynql/libasynql)](https://poggit.pmmp.io/ci/poggit/libasynql/libasynql)
Asynchronous SQL access library for PocketMine plugins.

## Prepared Statement File Format
A Prepared Statement File (PSF) contains the queries that a plugin uses. The content is valid SQL, so it is OK to edit with a normal SQL editor.

The PSF is annotated by "command lines", which start with `-- #`, followed by the command symbol, then the arguments. Between the `#` and the command symbol, there can be zero to infinite spaces or tabs; between the command symbol and the arguments, there can also be zero to infinite spaces or tabs. Between every two arguments, one to infinite spaces or tabs are required.

In other words, this is the regular expression for a command line:

```RegExp
-- #[ \t]*([!\{\}:])[ \t]*([^ \t]+)([ \t]+([^ \t]+))*
```

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

#### Arguments
##### VAR_NAME
The name of the variable. Any characters apart from spaces, tabs and colons are allowed. However, to comply with ordinary SQL editors, using "normal" symbols (e.g. variable names in other programming languages) is recommended.

##### VAR_TYPE
The variable type. Possible values:
- `string`
- `int`
- `float`
- `bool`

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

### Query text
Query text is not a command, but the non-commented part between the start and end commands of a query declaration.

Variables are interpolated in query text using the `:var` format. Note that libasynql uses a homebrew algorithm for identifying the variable positions, so they might be inaccurate.

### Overall example
See [mysql.sql](LibasynqlExample/resources/mysql.sql) and [sqlite3.sql](LibasynqlExample/resources/sqlite3.sql) in the example plugin.
