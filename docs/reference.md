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
* [NoEntityManager](#ormexceptionsnoentitymanager)
* [NotJoined](#ormexceptionsnotjoined)
* [NotScalar](#ormexceptionsnotscalar)
* [UnsupportedDriver](#ormexceptionsunsupporteddriver)


### ORM\QueryBuilder

* [Parenthesis](#ormquerybuilderparenthesis)
* [ParenthesisInterface](#ormquerybuilderparenthesisinterface)
* [QueryBuilder](#ormquerybuilderquerybuilder)
* [QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)


---

### ORM\DbConfig



#### Describes a database configuration






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **public** | `$type` | **string** | Dabase Type (mysql, pgsql or sqlite) |
| **public** | `$name` | **string** | Database name or path for sqlite |
| **public** | `$host` | **string** | Hostname or ip address |
| **public** | `$port` | **string** | Port for DBMS (defaults to 3306 for mysql and 5432 for pgsql) |
| **public** | `$user` | **string** | Database user |
| **public** | `$pass` | **string** | Database password |
| **public** | `$attributes` | **array** | PDO attributes |



#### Methods

* [__construct](#ormdbconfig__construct) Constructor
* [getDsn](#ormdbconfiggetdsn) Get the data source name

#### ORM\DbConfig::__construct

```php?start_inline=true
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
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  | Type of database (currently supported: `mysql`, `pgsql` and `sqlite`) |
| `$name` | **string**  | The name of the database or the path for sqlite |
| `$user` | **string**  | Username to use for connection |
| `$pass` | **string**  | Password |
| `$host` | **string**  | Hostname or IP address - defaults to `localhost` |
| `$port` | **string**  | Port - default ports (mysql: 3306, pgsql: 5432) |
| `$attributes` | **array**  | Array of PDO attributes |



#### ORM\DbConfig::getDsn

```php?start_inline=true
public function getDsn(): string
```

##### Get the data source name



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />





---

### ORM\Entity


**Implements:** [](#)

#### Definition of an entity

The instance of an entity represents a row of the table and the statics variables and methods describe the database
table.

This is the main part where your configuration efforts go. The following properties and methods are well documented
in the manual under [https://tflori.github.io/orm/entityDefinition.html](Entity Definition).


**See Also:**

* [Entity Definition](https://tflori.github.io/orm/entityDefinition.html)



#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$tableNameTemplate` | **string** | The template to use to calculate the table name. |
| **protected static** | `$namingSchemeTable` | **string** | The naming scheme to use for table names. |
| **protected static** | `$namingSchemeColumn` | **string** | The naming scheme to use for column names. |
| **protected static** | `$namingSchemeMethods` | **string** | The naming scheme to use for method names. |
| **protected static** | `$namingUsed` | **boolean** | Whether or not the naming got used |
| **protected static** | `$tableName` | **string** | Fixed table name (ignore other settings) |
| **protected static** | `$primaryKey` | **array&lt;string> &#124; string** | The variable(s) used for primary key. |
| **protected static** | `$columnAliases` | **array&lt;string>** | Fixed column names (ignore other settings) |
| **protected static** | `$columnPrefix` | **string** | A prefix for column names. |
| **protected static** | `$autoIncrement` | **boolean** | Whether or not the primary key is auto incremented. |
| **protected** | `$data` | **array&lt;mixed>** | The current data of a row. |
| **protected** | `$originalData` | **array&lt;mixed>** | The original data of the row. |
| **protected** | `$entityManager` | **EntityManager** | The entity manager from which this entity got created |



#### Methods

* [__construct](#ormentity__construct) Constructor
* [__get](#ormentity__get) Get the value from $var
* [__set](#ormentity__set) Set $var to $value
* [forceNamingScheme](#ormentityforcenamingscheme) Enforce $namingScheme to $name
* [getColumnName](#ormentitygetcolumnname) Get the column name of $name
* [getNamingSchemeColumn](#ormentitygetnamingschemecolumn) 
* [getNamingSchemeMethods](#ormentitygetnamingschememethods) 
* [getNamingSchemeTable](#ormentitygetnamingschemetable) 
* [getPrimaryKey](#ormentitygetprimarykey) Get the primary key
* [getPrimaryKeyVars](#ormentitygetprimarykeyvars) Get the primary key vars
* [getReflection](#ormentitygetreflection) Get reflection of the entity
* [getTableName](#ormentitygettablename) Get the table name
* [getTableNameTemplate](#ormentitygettablenametemplate) 
* [isAutoIncremented](#ormentityisautoincremented) Check if the table has a auto increment column.
* [isDirty](#ormentityisdirty) Checks if entity or $var got changed
* [onChange](#ormentityonchange) Empty event handler
* [onInit](#ormentityoninit) Empty event handler
* [postPersist](#ormentitypostpersist) Empty event handler
* [postUpdate](#ormentitypostupdate) Empty event handler
* [prePersist](#ormentityprepersist) Empty event handler
* [preUpdate](#ormentitypreupdate) Empty event handler
* [reset](#ormentityreset) Resets the entity or $var to original data
* [save](#ormentitysave) Save the entity to $entityManager
* [serialize](#ormentityserialize) String representation of data
* [setNamingSchemeColumn](#ormentitysetnamingschemecolumn) 
* [setNamingSchemeMethods](#ormentitysetnamingschememethods) 
* [setNamingSchemeTable](#ormentitysetnamingschemetable) 
* [setTableNameTemplate](#ormentitysettablenametemplate) 
* [unserialize](#ormentityunserialize) Constructs the object

#### ORM\Entity::__construct

```php?start_inline=true
final public function __construct(
    array $data = array(), \ORM\EntityManager $entityManager = null, 
    boolean $fromDatabase = false
): Entity
```

##### Constructor

It calls ::onInit() after initializing $data and $originalData.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array**  | The current data |
| `$entityManager` | **EntityManager**  | The EntityManager that created this entity |
| `$fromDatabase` | **boolean**  | Whether or not the data comes from database |



#### ORM\Entity::__get

```php?start_inline=true
public function __get( string $var ): mixed|null
```

##### Get the value from $var

If there is a custom getter this method get called instead.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed|null**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | The variable to get |



**See Also:**

* [Working with entities](https://tflori.github.io/orm/entities.html)

#### ORM\Entity::__set

```php?start_inline=true
public function __set( string $var, $value )
```

##### Set $var to $value

Tries to call custom setter before it stores the data directly. If there is a setter the setter needs to store
data that should be updated in the database to $data. Do not store data in $originalData as it will not be
written and give wrong results for dirty checking.

The onChange event is called after something got changed.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | The variable to change |
| `$value` | **mixed**  | The value to store |



**See Also:**

* [Working with entities](https://tflori.github.io/orm/entities.html)

#### ORM\Entity::forceNamingScheme

```php?start_inline=true
protected static function forceNamingScheme(
    string $name, string $namingScheme
): string
```

##### Enforce $namingScheme to $name

Supported naming schemes: snake_case, snake_lower, SNAKE_UPPER, Snake_Ucfirst, camelCase, StudlyCaps, lower
and UPPER.

**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  | The name of the var / column |
| `$namingScheme` | **string**  | The naming scheme to use |



#### ORM\Entity::getColumnName

```php?start_inline=true
public static function getColumnName( string $var ): string
```

##### Get the column name of $name

The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
to $namingSchemeColumn.

**ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to exactly
the same as getColumnName($name).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  |  |



#### ORM\Entity::getNamingSchemeColumn

```php?start_inline=true
public static function getNamingSchemeColumn(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getNamingSchemeMethods

```php?start_inline=true
public static function getNamingSchemeMethods(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getNamingSchemeTable

```php?start_inline=true
public static function getNamingSchemeTable(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getPrimaryKey

```php?start_inline=true
public function getPrimaryKey(): array
```

##### Get the primary key



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />



#### ORM\Entity::getPrimaryKeyVars

```php?start_inline=true
public static function getPrimaryKeyVars(): array
```

##### Get the primary key vars

The primary key can consist of multiple columns. You should configure the vars that are translated to these
columns.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Entity::getReflection

```php?start_inline=true
protected static function getReflection(): \ReflectionClass
```

##### Get reflection of the entity



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **\ReflectionClass**
<br />



#### ORM\Entity::getTableName

```php?start_inline=true
public static function getTableName(): string
```

##### Get the table name

The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
$tableName.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidName** or **\ORM\Exceptions\InvalidConfiguration**<br />



#### ORM\Entity::getTableNameTemplate

```php?start_inline=true
public static function getTableNameTemplate(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::isAutoIncremented

```php?start_inline=true
public static function isAutoIncremented(): boolean
```

##### Check if the table has a auto increment column.



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Entity::isDirty

```php?start_inline=true
public function isDirty( string $var = null ): boolean
```

##### Checks if entity or $var got changed



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | Check only this variable or all variables |



#### ORM\Entity::onChange

```php?start_inline=true
public function onChange( string $var, $oldValue, $value )
```

##### Empty event handler

Get called when something is changed with magic setter.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | The variable that got changed.merge(node.inheritedProperties) |
| `$oldValue` | **mixed**  | The old value of the variable |
| `$value` | **mixed**  | The new value of the variable |



#### ORM\Entity::onInit

```php?start_inline=true
public function onInit( boolean $new )
```

##### Empty event handler

Get called when the entity get initialized.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$new` | **boolean**  | Whether or not the entity is new or from database |



#### ORM\Entity::postPersist

```php?start_inline=true
public function postPersist()
```

##### Empty event handler

Get called after the entity got inserted in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::postUpdate

```php?start_inline=true
public function postUpdate()
```

##### Empty event handler

Get called after the entity got updated in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::prePersist

```php?start_inline=true
public function prePersist()
```

##### Empty event handler

Get called before the entity get inserted in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::preUpdate

```php?start_inline=true
public function preUpdate()
```

##### Empty event handler

Get called before the entity get updated in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::reset

```php?start_inline=true
public function reset( string $var = null )
```

##### Resets the entity or $var to original data



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | Reset only this variable or all variables |



#### ORM\Entity::save

```php?start_inline=true
public function save( \ORM\EntityManager $entityManager = null ): \ORM\Entity
```

##### Save the entity to $entityManager



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NoEntity** or **\ORM\Exceptions\NotScalar** or **\ORM\Exceptions\UnsupportedDriver** or **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\InvalidName** or **\ORM\Exceptions\NoEntityManager**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::serialize

```php?start_inline=true
public function serialize(): string
```

##### String representation of data



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



**See Also:**

* [http://php.net/manual/en/serializable.serialize.php](http://php.net/manual/en/serializable.serialize.php)

#### ORM\Entity::setNamingSchemeColumn

```php?start_inline=true
public static function setNamingSchemeColumn( string $namingSchemeColumn )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeColumn` | **string**  |  |



#### ORM\Entity::setNamingSchemeMethods

```php?start_inline=true
public static function setNamingSchemeMethods( string $namingSchemeMethods )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeMethods` | **string**  |  |



#### ORM\Entity::setNamingSchemeTable

```php?start_inline=true
public static function setNamingSchemeTable( string $namingSchemeTable )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeTable` | **string**  |  |



#### ORM\Entity::setTableNameTemplate

```php?start_inline=true
public static function setTableNameTemplate( string $tableNameTemplate )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableNameTemplate` | **string**  |  |



#### ORM\Entity::unserialize

```php?start_inline=true
public function unserialize( string $serialized )
```

##### Constructs the object



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$serialized` | **string**  | The string representation of data |



**See Also:**

* [http://php.net/manual/en/serializable.unserialize.php](http://php.net/manual/en/serializable.unserialize.php)



---

### ORM\EntityFetcher

**Extends:** [ORM\QueryBuilder\QueryBuilder](#ormquerybuilderquerybuilder)


#### Fetch entities from database

If you need more specific queries you write them yourself. If you need just more specific where clause you can pass
them to the *where() methods.

Supported:
 - joins with on clause (and alias)
 - joins with using (and alias)
 - where conditions
 - parenthesis
 - order by one or more columns / expressions
 - group by one or more columns / expressions
 - limit and offset
 - modifiers




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$tableName` | **string** | The table to query |
| **protected** | `$alias` | **string** | The alias of the main table |
| **protected** | `$columns` | **array** | Columns to fetch (null is equal to [&#039;*&#039;]) |
| **protected** | `$joins` | **array&lt;string>** | Joins get concatenated with space |
| **protected** | `$limit` | **integer** | Limit amount of rows |
| **protected** | `$offset` | **integer** | Offset to start from |
| **protected** | `$groupBy` | **array&lt;string>** | Group by conditions get concatenated with comma |
| **protected** | `$orderBy` | **array&lt;string>** | Order by conditions get concatenated with comma |
| **protected** | `$modifier` | **array&lt;string>** | Modifiers get concatenated with space |
| **protected** | `$entityManager` | **EntityManager** | The entity manager where entities get stored |
| **public static** | `$defaultEntityManager` | **EntityManager** | The default EntityManager to use to for quoting |
| **protected** | `$where` | **array&lt;string>** | Where conditions get concatenated with space |
| **protected** | `$onClose` | **callable** | Callback to close the parenthesis |
| **protected** | `$parent` | **QueryBuilder \ ParenthesisInterface** | Parent parenthesis or query |
| **protected** | `$class` | **string &#124; Entity** | The entity class that we want to fetch |
| **protected** | `$result` | ** \ PDOStatement** | The result object from PDO |
| **protected** | `$query` | **string &#124; QueryBuilder \ QueryBuilderInterface** | The query to execute (overwrites other settings) |
| **protected** | `$classMapping` | **array&lt;string[]>** | The class to alias mapping and vise versa |



#### Methods

* [__construct](#ormentityfetcher__construct) Constructor
* [all](#ormentityfetcherall) Fetch an array of entities
* [andParenthesis](#ormentityfetcherandparenthesis) Add a parenthesis with AND
* [andWhere](#ormentityfetcherandwhere) Add a where condition with AND.
* [close](#ormentityfetcherclose) Close parenthesis
* [column](#ormentityfetchercolumn) Add $column
* [columns](#ormentityfetchercolumns) Set $columns
* [convertPlaceholders](#ormentityfetcherconvertplaceholders) Replaces questionmarks in $expression with $args
* [fullJoin](#ormentityfetcherfulljoin) Full (outer) join $tableName with $options
* [getExpression](#ormentityfetchergetexpression) Get the expression
* [getQuery](#ormentityfetchergetquery) Get the query / select statement
* [getStatement](#ormentityfetchergetstatement) Query database and return result
* [groupBy](#ormentityfetchergroupby) Group By $column
* [join](#ormentityfetcherjoin) (Inner) join $tableName with $options
* [leftJoin](#ormentityfetcherleftjoin) Left (outer) join $tableName with $options
* [limit](#ormentityfetcherlimit) Set $limit
* [modifier](#ormentityfetchermodifier) Add $modifier
* [offset](#ormentityfetcheroffset) Set $offset
* [one](#ormentityfetcherone) Fetch one entity
* [orderBy](#ormentityfetcherorderby) Order By $column in $direction
* [orParenthesis](#ormentityfetcherorparenthesis) Add a parenthesis with OR
* [orWhere](#ormentityfetcherorwhere) Add a where condition with OR.
* [parenthesis](#ormentityfetcherparenthesis) Alias for andParenthesis
* [rightJoin](#ormentityfetcherrightjoin) Right (outer) join $tableName with $options
* [setQuery](#ormentityfetchersetquery) Set a raw query or use different QueryBuilder
* [where](#ormentityfetcherwhere) Alias for andWhere

#### ORM\EntityFetcher::__construct

```php?start_inline=true
public function __construct(
    \ORM\EntityManager $entityManager, \ORM\Entity $class
): EntityFetcher
```

##### Constructor

Create a select statement for $tableName with an object oriented interface.

It uses static::$defaultEntityManager if $entityManager is not given.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\InvalidName**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  | EntityManager where to store the fetched entities |
| `$class` | **Entity &#124; string**  | Class to fetch |



#### ORM\EntityFetcher::all

```php?start_inline=true
public function all( integer $limit ): array<\ORM\Entity>
```

##### Fetch an array of entities

When no $limit is set it fetches all entities in result set.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoConnection**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | Maximum number of entities to fetch |



#### ORM\EntityFetcher::andParenthesis

```php?start_inline=true
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\EntityFetcher::andWhere

```php?start_inline=true
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
andWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\EntityFetcher::close

```php?start_inline=true
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\EntityFetcher::column

```php?start_inline=true
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression to fetch |
| `$args` | **array**  | Arguments for expression |
| `$alias` | **string**  | Alias for the column |



#### ORM\EntityFetcher::columns

```php?start_inline=true
public function columns( array $columns = null ): QueryBuilder
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\EntityFetcher::convertPlaceholders

```php?start_inline=true
protected function convertPlaceholders(
    string $expression, array $args, boolean $translateCols = true
): string
```

##### Replaces questionmarks in $expression with $args

Additionally this method replaces "ClassName::var" with "alias.col" and "alias.var" with "alias.col" if
$translateCols is true (default).

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Argument(s) to insert |
| `$translateCols` | **boolean**  | Whether or not column names should be translated |



#### ORM\EntityFetcher::fullJoin

```php?start_inline=true
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::getExpression

```php?start_inline=true
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\EntityFetcher::getQuery

```php?start_inline=true
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\EntityFetcher::getStatement

```php?start_inline=true
private function getStatement(): \PDOStatement
```

##### Query database and return result

Queries the database with current query and returns the resulted PDOStatement.

If query failed it returns false. It also stores this failed result and to change the query afterwards will not
change the result.

**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\PDOStatement**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection**<br />



#### ORM\EntityFetcher::groupBy

```php?start_inline=true
public function groupBy( string $column, array $args = array() ): QueryBuilder
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::join

```php?start_inline=true
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::leftJoin

```php?start_inline=true
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::limit

```php?start_inline=true
public function limit( integer $limit ): QueryBuilder
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\EntityFetcher::modifier

```php?start_inline=true
public function modifier( string $modifier ): QueryBuilder
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\EntityFetcher::offset

```php?start_inline=true
public function offset( integer $offset ): QueryBuilder
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\EntityFetcher::one

```php?start_inline=true
public function one(): \ORM\Entity
```

##### Fetch one entity

If there is no more entity in the result set it returns null.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoConnection**<br />



#### ORM\EntityFetcher::orderBy

```php?start_inline=true
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilder
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::orParenthesis

```php?start_inline=true
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\EntityFetcher::orWhere

```php?start_inline=true
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
orWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\EntityFetcher::parenthesis

```php?start_inline=true
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\EntityFetcher::rightJoin

```php?start_inline=true
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::setQuery

```php?start_inline=true
public function setQuery( string $query, array $args = null ): $this
```

##### Set a raw query or use different QueryBuilder

For easier use and against sql injection it allows question mark placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string &#124; QueryBuilder\QueryBuilderInterface**  | Raw query string or a QueryBuilderInterface |
| `$args` | **array**  | The arguments for placeholders |



#### ORM\EntityFetcher::where

```php?start_inline=true
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
where('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |





---

### ORM\EntityManager



#### The EntityManager that manages the instances of Entities.





#### Constants

| Name | Value |
|------|-------|
| OPT_CONNECTION | `'connection'` |
| OPT_MYSQL_BOOLEAN_TRUE | `'mysqlTrue'` |
| OPT_MYSQL_BOOLEAN_FALSE | `'mysqlFalse'` |
| OPT_SQLITE_BOOLEAN_TRUE | `'sqliteTrue'` |
| OPT_SQLITE_BOOLEAN_FASLE | `'sqliteFalse'` |
| OPT_PGSQL_BOOLEAN_TRUE | `'pgsqlTrue'` |
| OPT_PGSQL_BOOLEAN_FALSE | `'pgsqlFalse'` |
| OPT_QUOTING_CHARACTER | `'quotingChar'` |
| OPT_IDENTIFIER_DIVIDER | `'identifierDivider'` |
| OPT_TABLE_NAME_TEMPLATE | `'tableNameTemplate'` |
| OPT_NAMING_SCHEME_TABLE | `'namingSchemeTable'` |
| OPT_NAMING_SCHEME_COLUMN | `'namingSchemeColumn'` |
| OPT_NAMING_SCHEME_METHODS | `'namingSchemeMethods'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$connection` | ** \ PDO &#124; callable &#124; DbConfig** | Connection to database |
| **protected** | `$map` | **array&lt;Entity[]>** | The Entity map |
| **protected** | `$options` | **array** | The options set for this instance |



#### Methods

* [__construct](#ormentitymanager__construct) Constructor
* [delete](#ormentitymanagerdelete) Delete $entity from database
* [escapeIdentifier](#ormentitymanagerescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormentitymanagerescapevalue) Returns $value formatted to use in a sql statement.
* [fetch](#ormentitymanagerfetch) Fetch one or more entities
* [getConnection](#ormentitymanagergetconnection) Get the pdo connection for $name.
* [getOption](#ormentitymanagergetoption) Get $option
* [map](#ormentitymanagermap) Map $entity in the entity map
* [setConnection](#ormentitymanagersetconnection) Add connection after instantiation
* [setOption](#ormentitymanagersetoption) Set $option to $value
* [sync](#ormentitymanagersync) Synchronizing $entity with database

#### ORM\EntityManager::__construct

```php?start_inline=true
public function __construct( array $options = array() ): EntityManager
```

##### Constructor



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` | **array**  | Options for the new EntityManager |



#### ORM\EntityManager::delete

```php?start_inline=true
public function delete( \ORM\Entity $entity ): boolean
```

##### Delete $entity from database

This method does not delete from the map - you can still receive the entity via fetch.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidName** or **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |



#### ORM\EntityManager::escapeIdentifier

```php?start_inline=true
public function escapeIdentifier( string $identifier ): string
```

##### Returns $identifier quoted for use in a sql statement



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$identifier` | **string**  | Identifier to quote |



#### ORM\EntityManager::escapeValue

```php?start_inline=true
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\EntityManager::fetch

```php?start_inline=true
public function fetch(
    string $class, $primaryKey = null
): \ORM\Entity|\ORM\EntityFetcher
```

##### Fetch one or more entities

With $primaryKey it tries to find this primary key in the entity map (carefully: mostly the database returns a
string and we do not convert them). If there is no entity in the entity map it tries to fetch the entity from
the database. The return value is then null (not found) or the entity.

Without $primaryKey it creates an entityFetcher and returns this.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity|\ORM\EntityFetcher**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NoEntity**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string &#124; Entity**  | The entity class you want to fetch |
| `$primaryKey` | **mixed**  | The primary key of the entity you want to fetch |



#### ORM\EntityManager::getConnection

```php?start_inline=true
public function getConnection(): \PDO
```

##### Get the pdo connection for $name.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\PDO**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection**<br />



#### ORM\EntityManager::getOption

```php?start_inline=true
public function getOption( $option ): mixed
```

##### Get $option



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` |   |  |



#### ORM\EntityManager::map

```php?start_inline=true
public function map( \ORM\Entity $entity, boolean $update = false ): \ORM\Entity
```

##### Map $entity in the entity map

Returns the given entity or an entity that previously got mapped. This is useful to work in every function with
the same object.

```php?start_inline=true
$user = $enitityManager->map(new User(['id' => 42]));
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |
| `$update` | **boolean**  | Update the entity map |



#### ORM\EntityManager::setConnection

```php?start_inline=true
public function setConnection( \PDO $connection )
```

##### Add connection after instantiation

The connection can be an array of parameters for DbConfig::__construct(), a callable function that returns a PDO
instance, an instance of DbConfig or a PDO instance itself.

When it is not a PDO instance the connection get established on first use.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$connection` | **\PDO &#124; callable &#124; DbConfig &#124; array**  | A configuration for (or a) PDO instance |



#### ORM\EntityManager::setOption

```php?start_inline=true
public function setOption( string $option, $value ): EntityManager
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  | One of OPT_* constants |
| `$value` | **mixed**  |  |



#### ORM\EntityManager::sync

```php?start_inline=true
public function sync( \ORM\Entity $entity, boolean $reset = false ): boolean
```

##### Synchronizing $entity with database

If $reset is true it also calls reset() on $entity.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NoEntity**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |
| `$reset` | **boolean**  | Reset entities current data |





---

### ORM\Exception

**Extends:** [](#)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\IncompletePrimaryKey

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\InvalidConfiguration

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\InvalidName

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\NoConnection

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\NoEntity

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\NoEntityManager

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\NotJoined

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\NotScalar

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\QueryBuilder\Parenthesis


**Implements:** [ORM\QueryBuilder\ParenthesisInterface](#ormquerybuilderparenthesisinterface)







#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$where` | **array&lt;string>** | Where conditions get concatenated with space |
| **protected** | `$onClose` | **callable** | Callback to close the parenthesis |
| **protected** | `$parent` | **ParenthesisInterface** | Parent parenthesis or query |



#### Methods

* [__construct](#ormquerybuilderparenthesis__construct) Constructor
* [andParenthesis](#ormquerybuilderparenthesisandparenthesis) Add a parenthesis with AND
* [andWhere](#ormquerybuilderparenthesisandwhere) Add a where condition with AND.
* [close](#ormquerybuilderparenthesisclose) Close parenthesis
* [getExpression](#ormquerybuilderparenthesisgetexpression) Get the expression
* [orParenthesis](#ormquerybuilderparenthesisorparenthesis) Add a parenthesis with OR
* [orWhere](#ormquerybuilderparenthesisorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderparenthesisparenthesis) Alias for andParenthesis
* [where](#ormquerybuilderparenthesiswhere) Alias for andWhere

#### ORM\QueryBuilder\Parenthesis::__construct

```php?start_inline=true
public function __construct(
    callable $onClose, \ORM\QueryBuilder\ParenthesisInterface $parent
): Parenthesis
```

##### Constructor

Create a parenthesis inside another parenthesis or a query.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$onClose` | **callable**  | Callable that gets executed when the parenthesis get closed |
| `$parent` | **ParenthesisInterface**  | Parent where createWhereCondition get executed |



#### ORM\QueryBuilder\Parenthesis::andParenthesis

```php?start_inline=true
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\Parenthesis::andWhere

```php?start_inline=true
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
andWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **Parenthesis**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\Parenthesis::close

```php?start_inline=true
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\Parenthesis::getExpression

```php?start_inline=true
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\Parenthesis::orParenthesis

```php?start_inline=true
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\Parenthesis::orWhere

```php?start_inline=true
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
orWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **Parenthesis**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\Parenthesis::parenthesis

```php?start_inline=true
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\Parenthesis::where

```php?start_inline=true
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
where('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **Parenthesis**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |





---

### ORM\QueryBuilder\ParenthesisInterface



#### Interface ParenthesisInterface








#### Methods

* [andParenthesis](#ormquerybuilderparenthesisinterfaceandparenthesis) Add a parenthesis with AND
* [andWhere](#ormquerybuilderparenthesisinterfaceandwhere) Add a where condition with AND.
* [close](#ormquerybuilderparenthesisinterfaceclose) Close parenthesis
* [getExpression](#ormquerybuilderparenthesisinterfacegetexpression) Get the expression
* [orParenthesis](#ormquerybuilderparenthesisinterfaceorparenthesis) Add a parenthesis with OR
* [orWhere](#ormquerybuilderparenthesisinterfaceorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderparenthesisinterfaceparenthesis) Alias for andParenthesis
* [where](#ormquerybuilderparenthesisinterfacewhere) Alias for andWhere

#### ORM\QueryBuilder\ParenthesisInterface::andParenthesis

```php?start_inline=true
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::andWhere

```php?start_inline=true
public function andWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
andWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::close

```php?start_inline=true
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::getExpression

```php?start_inline=true
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::orParenthesis

```php?start_inline=true
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::orWhere

```php?start_inline=true
public function orWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
orWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::parenthesis

```php?start_inline=true
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\ParenthesisInterface::where

```php?start_inline=true
public function where(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
where('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 


---

### ORM\QueryBuilder\QueryBuilder

**Extends:** [ORM\QueryBuilder\Parenthesis](#ormquerybuilderparenthesis)

**Implements:** [ORM\QueryBuilder\QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)

#### Build a ansi sql query / select statement

If you need more specific queries you write them yourself. If you need just more specific where clause you can pass
them to the *where() methods.

Supported:
 - joins with on clause (and alias)
 - joins with using (and alias)
 - where conditions
 - parenthesis
 - order by one or more columns / expressions
 - group by one or more columns / expressions
 - limit and offset




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$where` | **array&lt;string>** | Where conditions get concatenated with space |
| **protected** | `$onClose` | **callable** | Callback to close the parenthesis |
| **protected** | `$parent` | **ParenthesisInterface** | Parent parenthesis or query |
| **protected** | `$tableName` | **string** | The table to query |
| **protected** | `$alias` | **string** | The alias of the main table |
| **protected** | `$columns` | **array** | Columns to fetch (null is equal to [&#039;*&#039;]) |
| **protected** | `$joins` | **array&lt;string>** | Joins get concatenated with space |
| **protected** | `$limit` | **integer** | Limit amount of rows |
| **protected** | `$offset` | **integer** | Offset to start from |
| **protected** | `$groupBy` | **array&lt;string>** | Group by conditions get concatenated with comma |
| **protected** | `$orderBy` | **array&lt;string>** | Order by conditions get concatenated with comma |
| **protected** | `$modifier` | **array&lt;string>** | Modifiers get concatenated with space |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** | EntityManager to use for quoting |
| **public static** | `$defaultEntityManager` | ** \ ORM \ EntityManager** | The default EntityManager to use to for quoting |



#### Methods

* [__construct](#ormquerybuilderquerybuilder__construct) Constructor
* [andParenthesis](#ormquerybuilderquerybuilderandparenthesis) Add a parenthesis with AND
* [andWhere](#ormquerybuilderquerybuilderandwhere) Add a where condition with AND.
* [close](#ormquerybuilderquerybuilderclose) Close parenthesis
* [column](#ormquerybuilderquerybuildercolumn) Add $column
* [columns](#ormquerybuilderquerybuildercolumns) Set $columns
* [convertPlaceholders](#ormquerybuilderquerybuilderconvertplaceholders) Replaces question marks in $expression with $args
* [fullJoin](#ormquerybuilderquerybuilderfulljoin) Full (outer) join $tableName with $options
* [getExpression](#ormquerybuilderquerybuildergetexpression) Get the expression
* [getQuery](#ormquerybuilderquerybuildergetquery) Get the query / select statement
* [groupBy](#ormquerybuilderquerybuildergroupby) Group By $column
* [join](#ormquerybuilderquerybuilderjoin) (Inner) join $tableName with $options
* [leftJoin](#ormquerybuilderquerybuilderleftjoin) Left (outer) join $tableName with $options
* [limit](#ormquerybuilderquerybuilderlimit) Set $limit
* [modifier](#ormquerybuilderquerybuildermodifier) Add $modifier
* [offset](#ormquerybuilderquerybuilderoffset) Set $offset
* [orderBy](#ormquerybuilderquerybuilderorderby) Order By $column in $direction
* [orParenthesis](#ormquerybuilderquerybuilderorparenthesis) Add a parenthesis with OR
* [orWhere](#ormquerybuilderquerybuilderorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderquerybuilderparenthesis) Alias for andParenthesis
* [rightJoin](#ormquerybuilderquerybuilderrightjoin) Right (outer) join $tableName with $options
* [where](#ormquerybuilderquerybuilderwhere) Alias for andWhere

#### ORM\QueryBuilder\QueryBuilder::__construct

```php?start_inline=true
public function __construct(
    string $tableName, string $alias = '', 
    \ORM\EntityManager $entityManager = null
): QueryBuilder
```

##### Constructor

Create a select statement for $tableName with an object oriented interface.

It uses static::$defaultEntityManager if $entityManager is not given.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | The main table to use in FROM clause |
| `$alias` | **string**  | An alias for the table |
| `$entityManager` | **\ORM\EntityManager**  | EntityManager for quoting |



#### ORM\QueryBuilder\QueryBuilder::andParenthesis

```php?start_inline=true
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilder::andWhere

```php?start_inline=true
public function andWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
andWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilder::close

```php?start_inline=true
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilder::column

```php?start_inline=true
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression to fetch |
| `$args` | **array**  | Arguments for expression |
| `$alias` | **string**  | Alias for the column |



#### ORM\QueryBuilder\QueryBuilder::columns

```php?start_inline=true
public function columns( array $columns = null ): QueryBuilder
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\QueryBuilder\QueryBuilder::convertPlaceholders

```php?start_inline=true
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces question marks in $expression with $args



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Arguments for placeholders |



#### ORM\QueryBuilder\QueryBuilder::fullJoin

```php?start_inline=true
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::getExpression

```php?start_inline=true
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilder::getQuery

```php?start_inline=true
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilder::groupBy

```php?start_inline=true
public function groupBy( string $column, array $args = array() ): QueryBuilder
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::join

```php?start_inline=true
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::leftJoin

```php?start_inline=true
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::limit

```php?start_inline=true
public function limit( integer $limit ): QueryBuilder
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\QueryBuilder\QueryBuilder::modifier

```php?start_inline=true
public function modifier( string $modifier ): QueryBuilder
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\QueryBuilder\QueryBuilder::offset

```php?start_inline=true
public function offset( integer $offset ): QueryBuilder
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\QueryBuilder\QueryBuilder::orderBy

```php?start_inline=true
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilder
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::orParenthesis

```php?start_inline=true
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilder::orWhere

```php?start_inline=true
public function orWhere(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
orWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilder::parenthesis

```php?start_inline=true
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilder::rightJoin

```php?start_inline=true
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilder
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::where

```php?start_inline=true
public function where(
    string $column, string $operator = '', string $value = ''
): Parenthesis
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
where('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |





---

### ORM\QueryBuilder\QueryBuilderInterface

**Extends:** [ORM\QueryBuilder\ParenthesisInterface](#ormquerybuilderparenthesisinterface)


#### Interface QueryBuilderInterface





#### Constants

| Name | Value |
|------|-------|
| DIRECTION_ASCENDING | `'ASC'` |
| DIRECTION_DESCENDING | `'DESC'` |




#### Methods

* [andParenthesis](#ormquerybuilderquerybuilderinterfaceandparenthesis) Add a parenthesis with AND
* [andWhere](#ormquerybuilderquerybuilderinterfaceandwhere) Add a where condition with AND.
* [close](#ormquerybuilderquerybuilderinterfaceclose) Close parenthesis
* [column](#ormquerybuilderquerybuilderinterfacecolumn) Add $column
* [columns](#ormquerybuilderquerybuilderinterfacecolumns) Set $columns
* [fullJoin](#ormquerybuilderquerybuilderinterfacefulljoin) Full (outer) join $tableName with $options
* [getExpression](#ormquerybuilderquerybuilderinterfacegetexpression) Get the expression
* [getQuery](#ormquerybuilderquerybuilderinterfacegetquery) Get the query / select statement
* [groupBy](#ormquerybuilderquerybuilderinterfacegroupby) Group By $column
* [join](#ormquerybuilderquerybuilderinterfacejoin) (Inner) join $tableName with $options
* [leftJoin](#ormquerybuilderquerybuilderinterfaceleftjoin) Left (outer) join $tableName with $options
* [limit](#ormquerybuilderquerybuilderinterfacelimit) Set $limit
* [modifier](#ormquerybuilderquerybuilderinterfacemodifier) Add $modifier
* [offset](#ormquerybuilderquerybuilderinterfaceoffset) Set $offset
* [orderBy](#ormquerybuilderquerybuilderinterfaceorderby) Order By $column in $direction
* [orParenthesis](#ormquerybuilderquerybuilderinterfaceorparenthesis) Add a parenthesis with OR
* [orWhere](#ormquerybuilderquerybuilderinterfaceorwhere) Add a where condition with OR.
* [parenthesis](#ormquerybuilderquerybuilderinterfaceparenthesis) Alias for andParenthesis
* [rightJoin](#ormquerybuilderquerybuilderinterfacerightjoin) Right (outer) join $tableName with $options
* [where](#ormquerybuilderquerybuilderinterfacewhere) Alias for andWhere

#### ORM\QueryBuilder\QueryBuilderInterface::andParenthesis

```php?start_inline=true
public function andParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::andWhere

```php?start_inline=true
public function andWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
andWhere('name', '=' , 'John Doe')
andWhere('name = ?', 'John Doe')
andWhere('name', 'John Doe')
andWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::close

```php?start_inline=true
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::column

```php?start_inline=true
public function column(
    string $column, array $args = array(), string $alias = ''
): \ORM\QueryBuilder\QueryBuilder
```

##### Add $column

Optionally you can provide an expression with question marks as placeholders filled with $args.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilder**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression to fetch |
| `$args` | **array**  | Arguments for expression |
| `$alias` | **string**  | Alias for the column |



#### ORM\QueryBuilder\QueryBuilderInterface::columns

```php?start_inline=true
public function columns( $columns = null ): QueryBuilderInterface
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` |   |  |



#### ORM\QueryBuilder\QueryBuilderInterface::fullJoin

```php?start_inline=true
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::getExpression

```php?start_inline=true
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::getQuery

```php?start_inline=true
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::groupBy

```php?start_inline=true
public function groupBy(
    string $column, array $args = array()
): QueryBuilderInterface
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::join

```php?start_inline=true
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::leftJoin

```php?start_inline=true
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::limit

```php?start_inline=true
public function limit( integer $limit ): QueryBuilderInterface
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\QueryBuilder\QueryBuilderInterface::modifier

```php?start_inline=true
public function modifier( string $modifier ): QueryBuilderInterface
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\QueryBuilder\QueryBuilderInterface::offset

```php?start_inline=true
public function offset( integer $offset ): QueryBuilderInterface
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\QueryBuilder\QueryBuilderInterface::orderBy

```php?start_inline=true
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): QueryBuilderInterface
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::orParenthesis

```php?start_inline=true
public function orParenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::orWhere

```php?start_inline=true
public function orWhere(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
orWhere('name', '=' , 'John Doe')
orWhere('name = ?', 'John Doe')
orWhere('name', 'John Doe')
orWhere('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::parenthesis

```php?start_inline=true
public function parenthesis(): \ORM\QueryBuilder\ParenthesisInterface
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\ParenthesisInterface**
<br />



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\QueryBuilderInterface::rightJoin

```php?start_inline=true
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): QueryBuilderInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::where

```php?start_inline=true
public function where(
    string $column, string $operator = '', string $value = ''
): ParenthesisInterface
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php?start_inline=true
where('name', '=' , 'John Doe')
where('name = ?', 'John Doe')
where('name', 'John Doe')
where('name = ?', ['John Doe'])
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **QueryBuilderInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 


---

### ORM\Exceptions\UnsupportedDriver

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

