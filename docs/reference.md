---
layout: default
title: API Reference
permalink: /reference.html
---
## API Reference


### ORM

* [DbConfig](#ormdbconfig)
* [Entity](#ormentity)
* [EntityFetcher](#ormentityfetcher)
* [EntityManager](#ormentitymanager)
* [Exception](#ormexception)


### ORM\Exceptions

* [IncompletePrimaryKey](#ormexceptionsincompleteprimarykey)
* [InvalidConfiguration](#ormexceptionsinvalidconfiguration)
* [InvalidName](#ormexceptionsinvalidname)
* [NoConnection](#ormexceptionsnoconnection)
* [NoEntity](#ormexceptionsnoentity)
* [NotJoined](#ormexceptionsnotjoined)
* [NotScalar](#ormexceptionsnotscalar)


### ORM\QueryBuilder

* [Parenthesis](#ormquerybuilderparenthesis)
* [ParenthesisInterface](#ormquerybuilderparenthesisinterface)
* [QueryBuilder](#ormquerybuilderquerybuilder)
* [QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)


---

### ORM\DbConfig




**Describes a database configuration**







#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **public** | `$type` | **string** |  |
| **public** | `$name` | **string** |  |
| **public** | `$host` | **string** |  |
| **public** | `$port` | **string** |  |
| **public** | `$user` | **string** |  |
| **public** | `$pass` | **string** |  |
| **public** | `$attributes` | **array** |  |



#### Methods

* [__construct](#ormdbconfig__construct) Constructor
* [getDsn](#ormdbconfiggetdsn) Get the data source name

#### ORM\DbConfig::__construct

```php
public function __construct(
    string $type, string $name, string $user = null, string $pass = null, 
    string $host = null, string $port = null, array $attributes = array()
): DbConfig
```

##### Constructor

The constructor gets all parameters to establish a database connection and configure PDO instance.

Example:

```php?start_inline=true
$dbConfig = new DbConfig('mysql', 'my_db', 'my_user', 'my_secret', null, null, [
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string** | Type of database (currently supported: `mysql`, `pgsql` and `sqlite`) |
| `$name` | **string** | The name of the database or the path for sqlite |
| `$user` | **string** | Username to use for connection |
| `$pass` | **string** | Password |
| `$host` | **string** | Hostname or IP address - defaults to `localhost` |
| `$port` | **string** | Port - default ports (mysql: 3306, pgsql: 5432) |
| `$attributes` | **array** | Array of PDO attributes |



#### ORM\DbConfig::getDsn

```php
public function getDsn(): string
```

##### Get the data source name



**Visibility:** this method is **public**.






---
### ORM\Entity




**Definition of an entity**


The instance of an entity represents a row of the table and the statics variables and methods describe the database
table.




#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **public** | __*__`$tableNameTemplate` | **string** | The template to use to calculate the table name. |
| **public** | __*__`$namingSchemeTable` | **string** | The naming scheme to use for table names. |
| **public** | __*__`$namingSchemeColumn` | **string** | The naming scheme to use for column names. |
| **public** | __*__`$namingSchemeMethods` | **string** | The naming scheme to use for method names. |
| **public** | __*__`$connection` | **string** | The database connection to use. |
| **protected** | __*__`$tableName` | **string** | Fixed table name (ignore other settings) |
| **protected** | __*__`$primaryKey` | **array&lt;string>&#124;string** | The variable(s) used for primary key. |
| **protected** | __*__`$columnAliases` | **array&lt;string>** | Fixed column names (ignore other settings) |
| **protected** | __*__`$columnPrefix` | **string** | A prefix for column names. |
| **protected** | __*__`$autoIncrement` | **boolean** | Whether or not the primary key is auto incremented. |
| **protected** | __*__`$autoIncrementSequence` | **string** | Auto increment sequence to use for pgsql. |
| **protected** | `$data` | **array&lt;mixed>** | The current data of a row. |
| **protected** | `$originalData` | **array&lt;mixed>** | The original data of the row. |



#### Methods

* [__construct](#ormentity__construct) Entity constructor.
* [__get](#ormentity__get) Magic getter.
* [__set](#ormentity__set) Magic setter.
* [forceNamingScheme](#ormentityforcenamingscheme) Enforces $namingScheme to $name.
* [getAutoIncrementSequence](#ormentitygetautoincrementsequence) Get the sequence of the auto increment column (pgsql only).
* [getColumnName](#ormentitygetcolumnname) Get the column name of $name
* [getPrimaryKey](#ormentitygetprimarykey) Get the primary key for this Table
* [getReflection](#ormentitygetreflection) Get reflection of the entity class.
* [getTableName](#ormentitygettablename) Get the table name
* [isAutoIncremented](#ormentityisautoincremented) Whether or not the table has an auto incremented primary key.
* [isDirty](#ormentityisdirty) Checks if entity or $var got changed.
* [onChange](#ormentityonchange) Empty event handler.
* [onInit](#ormentityoninit) Empty event handler.
* [reset](#ormentityreset) Resets the entity or $var to original data.
* [save](#ormentitysave) Save the entity to $entityManager.

#### ORM\Entity::__construct

```php
final public function __construct(
    array $data = array(), boolean $fromDatabase = false
): Entity
```

##### Entity constructor.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array** |  |
| `$fromDatabase` | **boolean** |  |



#### ORM\Entity::__get

```php
public function __get( $var ): mixed|null
```

##### Magic getter.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **** |  |



#### ORM\Entity::__set

```php
public function __set( $var, $value )
```

##### Magic setter.

You can overwrite this for custom functionality but we recommend not to use the properties or setter (set*)
directly when they have to update the data stored in table.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **** |  |
| `$value` | **** |  |



#### ORM\Entity::forceNamingScheme

```php
protected static function forceNamingScheme(
    string $name, string $namingScheme
): string
```

##### Enforces $namingScheme to $name.



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string** |  |
| `$namingScheme` | **string** |  |



#### ORM\Entity::getAutoIncrementSequence

```php
public static function getAutoIncrementSequence(): string
```

##### Get the sequence of the auto increment column (pgsql only).



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.




#### ORM\Entity::getColumnName

```php
public static function getColumnName( string $var ): string
```

##### Get the column name of $name

The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
to $namingSchemeColumn.

**ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to exactly
the same as getColumnName($name).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string** |  |



**See Also:**

* [](https://tflori.github.io/orm/entityDefinition.html)

#### ORM\Entity::getPrimaryKey

```php
public static function getPrimaryKey(): array
```

##### Get the primary key for this Table



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.




#### ORM\Entity::getReflection

```php
protected static function getReflection(): \ReflectionClass
```

##### Get reflection of the entity class.



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.




#### ORM\Entity::getTableName

```php
public static function getTableName(): string
```

##### Get the table name

The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
$tableName.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.




#### ORM\Entity::isAutoIncremented

```php
public static function isAutoIncremented(): boolean
```

##### Whether or not the table has an auto incremented primary key.



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.




#### ORM\Entity::isDirty

```php
public function isDirty( string $var = null ): boolean
```

##### Checks if entity or $var got changed.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string** |  |



#### ORM\Entity::onChange

```php
public function onChange( string $var, $oldValue, $value )
```

##### Empty event handler.

Get called when something is changed with magic setter.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string** |  |
| `$oldValue` | **mixed** |  |
| `$value` | **mixed** |  |



#### ORM\Entity::onInit

```php
public function onInit( boolean $new )
```

##### Empty event handler.

Get called when the entity get initialized.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$new` | **boolean** |  |



#### ORM\Entity::reset

```php
public function reset( string $var = null )
```

##### Resets the entity or $var to original data.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string** |  |



#### ORM\Entity::save

```php
public function save( \ORM\EntityManager $entityManager )
```

##### Save the entity to $entityManager.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager** |  |





---
### ORM\EntityFetcher

**Extends:** [ORM\QueryBuilder\QueryBuilder](#ormquerybuilderquerybuilder)



**Class EntityFetcher**







#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **protected** | `$entityManager` | **\ORM\EntityManager** |  |
| **protected** | `$class` | **string&#124;\ORM\Entity** |  |
| **protected** | `$result` | **\PDOStatement** |  |
| **protected** | `$query` | **string** |  |
| **protected** | `$classMapping` | **array** |  |



#### Methods

* [__construct](#ormentityfetcher__construct) 
* [all](#ormentityfetcherall) Fetch an array of entities
* [andParenthesis](#ormentityfetcherandparenthesis) Add a parenthesis with AND.
* [andWhere](#ormentityfetcherandwhere) Add a where condition with AND.
* [close](#ormentityfetcherclose) 
* [column](#ormentityfetchercolumn) Add $column
* [columns](#ormentityfetchercolumns) Set $columns
* [convertPlaceholders](#ormentityfetcherconvertplaceholders) Replaces questionmarks in $expression with $args
* [createJoin](#ormentityfetchercreatejoin) Creates the $join statement.
* [fullJoin](#ormentityfetcherfulljoin) Right (outer) join $tableName with $options
* [getParenthesis](#ormentityfetchergetparenthesis) 
* [getQuery](#ormentityfetchergetquery) 
* [getStatement](#ormentityfetchergetstatement) 
* [getWhereCondition](#ormentityfetchergetwherecondition) 
* [groupBy](#ormentityfetchergroupby) Group By $column
* [join](#ormentityfetcherjoin) (Inner) join $tableName with $options
* [leftJoin](#ormentityfetcherleftjoin) Left (outer) join $tableName with $options
* [limit](#ormentityfetcherlimit) Set $limit
* [modifier](#ormentityfetchermodifier) Add $modifier
* [offset](#ormentityfetcheroffset) Set $offset
* [one](#ormentityfetcherone) Fetch one entity
* [orderBy](#ormentityfetcherorderby) Order By $column in $direction
* [orParenthesis](#ormentityfetcherorparenthesis) Add a parenthesis with OR.
* [orWhere](#ormentityfetcherorwhere) Add a where condition with OR.
* [parenthesis](#ormentityfetcherparenthesis) Add a parenthesis with AND. Alias for andParenthesis.
* [rightJoin](#ormentityfetcherrightjoin) Right (outer) join $tableName with $options
* [setQuery](#ormentityfetchersetquery) 
* [where](#ormentityfetcherwhere) Add a where condition with AND. Alias for andWhere.

#### ORM\EntityFetcher::__construct

```php
public function __construct(
    callable $onClose, \ORM\QueryBuilder\ParenthesisInterface $parent
): Parenthesis
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$onClose` | **callable** |  |
| `$parent` | **\ORM\QueryBuilder\ParenthesisInterface** |  |



#### ORM\EntityFetcher::all

```php
public function all( integer $limit ): array<\ORM\Entity>
```

##### Fetch an array of entities

When no $limit is set it fetches all entities in current cursor.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer** | Maximum number of entities to fetch |



#### ORM\EntityFetcher::andParenthesis

```php
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND.



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\EntityFetcher::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### 



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::column

```php
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |
| `$alias` | **string** |  |



#### ORM\EntityFetcher::columns

```php
public function columns( array $columns = null ): QueryBuilder
```

##### Set $columns



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array** |  |



#### ORM\EntityFetcher::convertPlaceholders

```php
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces questionmarks in $expression with $args



**Visibility:** this method is **protected**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string** |  |
| `$args` | **array&#124;mixed** |  |



#### ORM\EntityFetcher::createJoin

```php
protected function createJoin(
    string $join, string $tableName, string $expression, string $alias, 
    array $args, boolean $empty
): QueryBuilder
```

##### Creates the $join statement.



**Visibility:** this method is **protected**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$join` | **string** |  |
| `$tableName` | **string** |  |
| `$expression` | **string** |  |
| `$alias` | **string** |  |
| `$args` | **array&#124;mixed** |  |
| `$empty` | **boolean** |  |



#### ORM\EntityFetcher::fullJoin

```php
public function fullJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::getParenthesis

```php
public function getParenthesis(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::getQuery

```php
public function getQuery(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::getStatement

```php
private function getStatement(): \PDOStatement
```

##### 



**Visibility:** this method is **private**.




#### ORM\EntityFetcher::getWhereCondition

```php
public function getWhereCondition( $column, $operator = '', $value = '' )
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **** |  |
| `$operator` | **** |  |
| `$value` | **** |  |



#### ORM\EntityFetcher::groupBy

```php
public function groupBy( string $column, array $args = array() ): QueryBuilder
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::join

```php
public function join(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::leftJoin

```php
public function leftJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::limit

```php
public function limit( integer $limit ): QueryBuilder
```

##### Set $limit



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer** |  |



#### ORM\EntityFetcher::modifier

```php
public function modifier( string $modifier ): QueryBuilder
```

##### Add $modifier



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string** |  |



#### ORM\EntityFetcher::offset

```php
public function offset( integer $offset ): QueryBuilder
```

##### Set $offset



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer** |  |



#### ORM\EntityFetcher::one

```php
public function one(): \ORM\Entity
```

##### Fetch one entity



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilder
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$direction` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::orParenthesis

```php
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR.



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\EntityFetcher::parenthesis

```php
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND. Alias for andParenthesis.



**Visibility:** this method is **public**.




#### ORM\EntityFetcher::rightJoin

```php
public function rightJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::setQuery

```php
public function setQuery( $query, array $args = null )
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **** |  |
| `$args` | **array** |  |



#### ORM\EntityFetcher::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND. Alias for andWhere.

QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |





---
### ORM\EntityManager




**The EntityManager that manages the instances of Entities.**






#### Constants

| Name | Value |
|------|-------|
| OPT_DEFAULT_CONNECTION | `'connection'` |
| OPT_CONNECTIONS | `'connections'` |
| OPT_MYSQL_BOOLEAN_TRUE | `'mysqlTrue'` |
| OPT_MYSQL_BOOLEAN_FALSE | `'mysqlFalse'` |
| OPT_SQLITE_BOOLEAN_TRUE | `'sqliteTrue'` |
| OPT_SQLITE_BOOLEAN_FASLE | `'sqliteFalse'` |
| OPT_PGSQL_BOOLEAN_TRUE | `'pgsqlTrue'` |
| OPT_PGSQL_BOOLEAN_FALSE | `'pgsqlFalse'` |


#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **protected** | `$connections` | **** |  |
| **protected** | `$map` | **array&lt;\ORM\Entity[]>** |  |
| **protected** | `$options` | **** |  |



#### Methods

* [__construct](#ormentitymanager__construct) 
* [convertValue](#ormentitymanagerconvertvalue) Returns the given $value formatted to use in a sql statement.
* [fetch](#ormentitymanagerfetch) 
* [getConnection](#ormentitymanagergetconnection) Get the pdo connection for $name.
* [map](#ormentitymanagermap) 
* [setConnection](#ormentitymanagersetconnection) Set the connection $name to $connection.

#### ORM\EntityManager::__construct

```php
public function __construct( array $options = array() ): EntityManager
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` | **array** |  |



#### ORM\EntityManager::convertValue

```php
public function convertValue( $value, string $connection = 'default' ): string
```

##### Returns the given $value formatted to use in a sql statement.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed** | The variable that should be returned in SQL syntax |
| `$connection` | **string** | The connection to use for quoting |



#### ORM\EntityManager::fetch

```php
public function fetch(
    string $class, $primaryKey = null
): \ORM\Entity|\ORM\EntityFetcher
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string&#124;\ORM\Entity** |  |
| `$primaryKey` | **mixed** |  |



#### ORM\EntityManager::getConnection

```php
public function getConnection( string $name = 'default' ): \PDO
```

##### Get the pdo connection for $name.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string** |  |



#### ORM\EntityManager::map

```php
public function map( \ORM\Entity $entity ): \ORM\Entity
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity** |  |



#### ORM\EntityManager::setConnection

```php
public function setConnection( string $name, \PDO $connection )
```

##### Set the connection $name to $connection.



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string** |  |
| `$connection` | **\PDO&#124;callable&#124;\ORM\DbConfig** |  |





---
### ORM\Exception

**Extends:** [](#)



**Base exception for ORM**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\IncompletePrimaryKey

**Extends:** [ORM\Exception](#ormexception)



**Class IncompletePrimaryKey**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\InvalidConfiguration

**Extends:** [ORM\Exception](#ormexception)



**Class InvalidConfiguration**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\InvalidName

**Extends:** [ORM\Exception](#ormexception)



**Class InvalidName**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\NoConnection

**Extends:** [ORM\Exception](#ormexception)



**Class NoConnection**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\NoEntity

**Extends:** [ORM\Exception](#ormexception)



**Class NoEntity**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\NotJoined

**Extends:** [ORM\Exception](#ormexception)



**Class NotJoined**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\Exceptions\NotScalar

**Extends:** [ORM\Exception](#ormexception)



**Class NotScalar**


Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---
### ORM\QueryBuilder\Parenthesis


**Implements:** [ORM\QueryBuilder\ParenthesisInterface](#ormquerybuilderparenthesisinterface)


**Class Parenthesis**







#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **protected** | `$where` | **array&lt;string>** |  |
| **protected** | `$onClose` | **callable** |  |
| **protected** | `$entityManager` | **\ORM\EntityManager** |  |
| **protected** | `$connection` | **string** |  |
| **protected** | `$parent` | **\ORM\QueryBuilder\ParenthesisInterface** |  |



#### Methods

* [__construct](#ormquerybuilderparenthesis__construct) 
* [andParenthesis](#ormquerybuilderparenthesisandparenthesis) Add a parenthesis with AND.
* [andWhere](#ormquerybuilderparenthesisandwhere) Add a where condition with AND.
* [close](#ormquerybuilderparenthesisclose) 
* [getParenthesis](#ormquerybuilderparenthesisgetparenthesis) 
* [getWhereCondition](#ormquerybuilderparenthesisgetwherecondition) 
* [orParenthesis](#ormquerybuilderparenthesisorparenthesis) Add a parenthesis with OR.
* [orWhere](#ormquerybuilderparenthesisorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderparenthesisparenthesis) Add a parenthesis with AND. Alias for andParenthesis.
* [where](#ormquerybuilderparenthesiswhere) Add a where condition with AND. Alias for andWhere.

#### ORM\QueryBuilder\Parenthesis::__construct

```php
public function __construct(
    callable $onClose, \ORM\QueryBuilder\ParenthesisInterface $parent
): Parenthesis
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$onClose` | **callable** |  |
| `$parent` | **\ORM\QueryBuilder\ParenthesisInterface** |  |



#### ORM\QueryBuilder\Parenthesis::andParenthesis

```php
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\Parenthesis::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\Parenthesis::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\Parenthesis::getParenthesis

```php
public function getParenthesis(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\Parenthesis::getWhereCondition

```php
public function getWhereCondition( $column, $operator = '', $value = '' )
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **** |  |
| `$operator` | **** |  |
| `$value` | **** |  |



#### ORM\QueryBuilder\Parenthesis::orParenthesis

```php
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\Parenthesis::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\Parenthesis::parenthesis

```php
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND. Alias for andParenthesis.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\Parenthesis::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND. Alias for andWhere.

QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |





---
### ORM\QueryBuilder\ParenthesisInterface




**Interface ParenthesisInterface**









#### Methods

* [andParenthesis](#ormquerybuilderparenthesisinterfaceandparenthesis) Add a parenthesis with AND.
* [andWhere](#ormquerybuilderparenthesisinterfaceandwhere) Add a where condition with AND.
* [close](#ormquerybuilderparenthesisinterfaceclose) 
* [getParenthesis](#ormquerybuilderparenthesisinterfacegetparenthesis) 
* [orParenthesis](#ormquerybuilderparenthesisinterfaceorparenthesis) Add a parenthesis with OR.
* [orWhere](#ormquerybuilderparenthesisinterfaceorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderparenthesisinterfaceparenthesis) Add a parenthesis with AND. Alias for andParenthesis.
* [where](#ormquerybuilderparenthesisinterfacewhere) Add a where condition with AND. Alias for andWhere.

#### ORM\QueryBuilder\ParenthesisInterface::andParenthesis

```php
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\ParenthesisInterface::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\ParenthesisInterface::getParenthesis

```php
public function getParenthesis(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\ParenthesisInterface::orParenthesis

```php
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\ParenthesisInterface::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::parenthesis

```php
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND. Alias for andParenthesis.



**Visibility:** this method is **public**.




**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\ParenthesisInterface::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND. Alias for andWhere.

QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 


---
### ORM\QueryBuilder\QueryBuilder

**Extends:** [ORM\QueryBuilder\Parenthesis](#ormquerybuilderparenthesis)

**Implements:** [ORM\QueryBuilder\QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)


**Class QueryBuilder**







#### Properties

| Visibility | Name | Type | Description |
|------------|------|------|-------------|
| **protected** | `$tableName` | **string** |  |
| **protected** | `$alias` | **string** |  |
| **protected** | `$columns` | **array** |  |
| **protected** | `$joins` | **array** |  |
| **protected** | `$where` | **array&lt;string>** |  |
| **protected** | `$limit` | **integer** |  |
| **protected** | `$offset` | **integer** |  |
| **protected** | `$groupBy` | **array&lt;string>** |  |
| **protected** | `$orderBy` | **array&lt;string>** |  |
| **protected** | `$modifier` | **array&lt;string>** |  |
| **public** | __*__`$defaultEntityManager` | **\ORM\EntityManager** | The default EntityManager to use to for quoting |
| **public** | __*__`$defaultConnection` | **string** | The default connection to use for quoting |



#### Methods

* [__construct](#ormquerybuilderquerybuilder__construct) 
* [andParenthesis](#ormquerybuilderquerybuilderandparenthesis) Add a parenthesis with AND.
* [andWhere](#ormquerybuilderquerybuilderandwhere) Add a where condition with AND.
* [close](#ormquerybuilderquerybuilderclose) 
* [column](#ormquerybuilderquerybuildercolumn) Add $column
* [columns](#ormquerybuilderquerybuildercolumns) Set $columns
* [convertPlaceholders](#ormquerybuilderquerybuilderconvertplaceholders) Replaces questionmarks in $expression with $args
* [createJoin](#ormquerybuilderquerybuildercreatejoin) Creates the $join statement.
* [fullJoin](#ormquerybuilderquerybuilderfulljoin) Right (outer) join $tableName with $options
* [getParenthesis](#ormquerybuilderquerybuildergetparenthesis) 
* [getQuery](#ormquerybuilderquerybuildergetquery) 
* [getWhereCondition](#ormquerybuilderquerybuildergetwherecondition) 
* [groupBy](#ormquerybuilderquerybuildergroupby) Group By $column
* [join](#ormquerybuilderquerybuilderjoin) (Inner) join $tableName with $options
* [leftJoin](#ormquerybuilderquerybuilderleftjoin) Left (outer) join $tableName with $options
* [limit](#ormquerybuilderquerybuilderlimit) Set $limit
* [modifier](#ormquerybuilderquerybuildermodifier) Add $modifier
* [offset](#ormquerybuilderquerybuilderoffset) Set $offset
* [orderBy](#ormquerybuilderquerybuilderorderby) Order By $column in $direction
* [orParenthesis](#ormquerybuilderquerybuilderorparenthesis) Add a parenthesis with OR.
* [orWhere](#ormquerybuilderquerybuilderorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderquerybuilderparenthesis) Add a parenthesis with AND. Alias for andParenthesis.
* [rightJoin](#ormquerybuilderquerybuilderrightjoin) Right (outer) join $tableName with $options
* [where](#ormquerybuilderquerybuilderwhere) Add a where condition with AND. Alias for andWhere.

#### ORM\QueryBuilder\QueryBuilder::__construct

```php
public function __construct(
    callable $onClose, \ORM\QueryBuilder\ParenthesisInterface $parent
): Parenthesis
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$onClose` | **callable** |  |
| `$parent` | **\ORM\QueryBuilder\ParenthesisInterface** |  |



#### ORM\QueryBuilder\QueryBuilder::andParenthesis

```php
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\QueryBuilder::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::column

```php
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |
| `$alias` | **string** |  |



#### ORM\QueryBuilder\QueryBuilder::columns

```php
public function columns( array $columns = null ): QueryBuilder
```

##### Set $columns



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::convertPlaceholders

```php
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces questionmarks in $expression with $args



**Visibility:** this method is **protected**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string** |  |
| `$args` | **array&#124;mixed** |  |



#### ORM\QueryBuilder\QueryBuilder::createJoin

```php
protected function createJoin(
    string $join, string $tableName, string $expression, string $alias, 
    array $args, boolean $empty
): QueryBuilder
```

##### Creates the $join statement.



**Visibility:** this method is **protected**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$join` | **string** |  |
| `$tableName` | **string** |  |
| `$expression` | **string** |  |
| `$alias` | **string** |  |
| `$args` | **array&#124;mixed** |  |
| `$empty` | **boolean** |  |



#### ORM\QueryBuilder\QueryBuilder::fullJoin

```php
public function fullJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::getParenthesis

```php
public function getParenthesis(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::getQuery

```php
public function getQuery(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::getWhereCondition

```php
public function getWhereCondition( $column, $operator = '', $value = '' )
```

##### 



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **** |  |
| `$operator` | **** |  |
| `$value` | **** |  |



#### ORM\QueryBuilder\QueryBuilder::groupBy

```php
public function groupBy( string $column, array $args = array() ): QueryBuilder
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::join

```php
public function join(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::leftJoin

```php
public function leftJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::limit

```php
public function limit( integer $limit ): QueryBuilder
```

##### Set $limit



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer** |  |



#### ORM\QueryBuilder\QueryBuilder::modifier

```php
public function modifier( string $modifier ): QueryBuilder
```

##### Add $modifier



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string** |  |



#### ORM\QueryBuilder\QueryBuilder::offset

```php
public function offset( integer $offset ): QueryBuilder
```

##### Set $offset



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer** |  |



#### ORM\QueryBuilder\QueryBuilder::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilder
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$direction` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::orParenthesis

```php
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\QueryBuilder::parenthesis

```php
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND. Alias for andParenthesis.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilder::rightJoin

```php
public function rightJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilder::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND. Alias for andWhere.

QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |





---
### ORM\QueryBuilder\QueryBuilderInterface

**Extends:** [ORM\QueryBuilder\ParenthesisInterface](#ormquerybuilderparenthesisinterface)



**Interface QueryBuilderInterface**






#### Constants

| Name | Value |
|------|-------|
| DIRECTION_ASCENDING | `'ASC'` |
| DIRECTION_DESCENDING | `'DESC'` |




#### Methods

* [andParenthesis](#ormquerybuilderquerybuilderinterfaceandparenthesis) Add a parenthesis with AND.
* [andWhere](#ormquerybuilderquerybuilderinterfaceandwhere) Add a where condition with AND.
* [close](#ormquerybuilderquerybuilderinterfaceclose) 
* [column](#ormquerybuilderquerybuilderinterfacecolumn) Add $column
* [columns](#ormquerybuilderquerybuilderinterfacecolumns) Set $columns
* [fullJoin](#ormquerybuilderquerybuilderinterfacefulljoin) Right (outer) join $tableName with $options
* [getParenthesis](#ormquerybuilderquerybuilderinterfacegetparenthesis) 
* [getQuery](#ormquerybuilderquerybuilderinterfacegetquery) 
* [groupBy](#ormquerybuilderquerybuilderinterfacegroupby) Group By $column
* [join](#ormquerybuilderquerybuilderinterfacejoin) (Inner) join $tableName with $options
* [leftJoin](#ormquerybuilderquerybuilderinterfaceleftjoin) Left (outer) join $tableName with $options
* [limit](#ormquerybuilderquerybuilderinterfacelimit) Set $limit
* [modifier](#ormquerybuilderquerybuilderinterfacemodifier) Add $modifier
* [offset](#ormquerybuilderquerybuilderinterfaceoffset) Set $offset
* [orderBy](#ormquerybuilderquerybuilderinterfaceorderby) Order By $column in $direction
* [orParenthesis](#ormquerybuilderquerybuilderinterfaceorparenthesis) Add a parenthesis with OR.
* [orWhere](#ormquerybuilderquerybuilderinterfaceorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderquerybuilderinterfaceparenthesis) Add a parenthesis with AND. Alias for andParenthesis.
* [rightJoin](#ormquerybuilderquerybuilderinterfacerightjoin) Right (outer) join $tableName with $options
* [where](#ormquerybuilderquerybuilderinterfacewhere) Add a where condition with AND. Alias for andWhere.

#### ORM\QueryBuilder\QueryBuilderInterface::andParenthesis

```php
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilderInterface::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilderInterface::column

```php
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |
| `$alias` | **string** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::columns

```php
public function columns( $columns = null ): QueryBuilderInterface
```

##### Set $columns



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::fullJoin

```php
public function fullJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::getParenthesis

```php
public function getParenthesis(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilderInterface::getQuery

```php
public function getQuery(): string
```

##### 



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilderInterface::groupBy

```php
public function groupBy(
    string $column, array $args = array()
): QueryBuilderInterface
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::join

```php
public function join(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::leftJoin

```php
public function leftJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::limit

```php
public function limit( integer $limit ): QueryBuilderInterface
```

##### Set $limit



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::modifier

```php
public function modifier( string $modifier ): QueryBuilderInterface
```

##### Add $modifier



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::offset

```php
public function offset( integer $offset ): QueryBuilderInterface
```

##### Set $offset



**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilderInterface
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** |  |
| `$direction` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::orParenthesis

```php
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR.



**Visibility:** this method is **public**.




#### ORM\QueryBuilder\QueryBuilderInterface::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::parenthesis

```php
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND. Alias for andParenthesis.



**Visibility:** this method is **public**.




**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\QueryBuilderInterface::rightJoin

```php
public function rightJoin(
    string $tableName, $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string** |  |
| `$expression` | **** |  |
| `$alias` | **string** |  |
| `$args` | **array** |  |



#### ORM\QueryBuilder\QueryBuilderInterface::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND. Alias for andWhere.

QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:
```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
```

**Visibility:** this method is **public**.


**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string** | Column or expression with placeholders |
| `$operator` | **string&#124;array** | Operator, value or array of values |
| `$value` | **string** | Value (required if used with operator) |



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 


---
