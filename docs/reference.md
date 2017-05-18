---
layout: default
title: API Reference
permalink: /reference.html
---
## API Reference


### ORM

* [Dbal](#ormdbal)
* [DbConfig](#ormdbconfig)
* [Entity](#ormentity)
* [EntityFetcher](#ormentityfetcher)
* [EntityManager](#ormentitymanager)
* [Exception](#ormexception)
* [Relation](#ormrelation)


### ORM\Exceptions

* [IncompletePrimaryKey](#ormexceptionsincompleteprimarykey)
* [InvalidConfiguration](#ormexceptionsinvalidconfiguration)
* [InvalidName](#ormexceptionsinvalidname)
* [InvalidRelation](#ormexceptionsinvalidrelation)
* [NoConnection](#ormexceptionsnoconnection)
* [NoEntity](#ormexceptionsnoentity)
* [NoEntityManager](#ormexceptionsnoentitymanager)
* [NoOperator](#ormexceptionsnooperator)
* [NotJoined](#ormexceptionsnotjoined)
* [NotScalar](#ormexceptionsnotscalar)
* [UndefinedRelation](#ormexceptionsundefinedrelation)
* [UnsupportedDriver](#ormexceptionsunsupporteddriver)


### ORM\Dbal

* [Column](#ormdbalcolumn)
* [Mysql](#ormdbalmysql)
* [Other](#ormdbalother)
* [Pgsql](#ormdbalpgsql)
* [Sqlite](#ormdbalsqlite)
* [Type](#ormdbaltype)
* [TypeInterface](#ormdbaltypeinterface)


### ORM\Dbal\Type

* [Boolean](#ormdbaltypeboolean)
* [DateTime](#ormdbaltypedatetime)
* [Double](#ormdbaltypedouble)
* [Enum](#ormdbaltypeenum)
* [Integer](#ormdbaltypeinteger)
* [Json](#ormdbaltypejson)
* [Set](#ormdbaltypeset)
* [Text](#ormdbaltypetext)
* [Time](#ormdbaltypetime)
* [VarChar](#ormdbaltypevarchar)


### ORM\Relation

* [ManyToMany](#ormrelationmanytomany)
* [OneToMany](#ormrelationonetomany)
* [OneToOne](#ormrelationonetoone)
* [Owner](#ormrelationowner)


### ORM\QueryBuilder

* [Parenthesis](#ormquerybuilderparenthesis)
* [ParenthesisInterface](#ormquerybuilderparenthesisinterface)
* [QueryBuilder](#ormquerybuilderquerybuilder)
* [QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)


---

### ORM\Dbal\Type\Boolean

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Boolean data type









---

### ORM\Dbal\Column



#### Describes a column of a database table






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** |  |
| **protected** | `$type` | **TypeInterface** |  |
| **protected** | `$hasDefault` | **boolean** |  |
| **protected** | `$isNullable` | **boolean** |  |



#### Methods

* [__construct](#ormdbalcolumn__construct) Column constructor.
* [factory](#ormdbalcolumnfactory) Returns a new column with params from $columnDefinition
* [getName](#ormdbalcolumngetname) 
* [getType](#ormdbalcolumngettype) 
* [hasDefault](#ormdbalcolumnhasdefault) 
* [isNullable](#ormdbalcolumnisnullable) 

#### ORM\Dbal\Column::__construct

```php?start_inline=true
public function __construct(
    string $name, \ORM\Dbal\TypeInterface $type, boolean $hasDefault, 
    boolean $isNullable
): Column
```

##### Column constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$type` | **TypeInterface**  |  |
| `$hasDefault` | **boolean**  |  |
| `$isNullable` | **boolean**  |  |



#### ORM\Dbal\Column::factory

```php?start_inline=true
public static function factory(
    array $columnDefinition, \ORM\Dbal\TypeInterface $type
): static
```

##### Returns a new column with params from $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |
| `$type` | **TypeInterface**  |  |



#### ORM\Dbal\Column::getName

```php?start_inline=true
public function getName(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Column::getType

```php?start_inline=true
public function getType(): \ORM\Dbal\Type
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Type**
<br />



#### ORM\Dbal\Column::hasDefault

```php?start_inline=true
public function hasDefault(): boolean
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Dbal\Column::isNullable

```php?start_inline=true
public function isNullable(): boolean
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />





---

### ORM\Dbal\Type\DateTime

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Date and datetime data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$precision` | **integer** |  |



#### Methods

* [__construct](#ormdbaltypedatetime__construct) DateTime constructor.
* [factory](#ormdbaltypedatetimefactory) 
* [fromDefinition](#ormdbaltypedatetimefromdefinition) Create this type from $columnDefinition.

#### ORM\Dbal\Type\DateTime::__construct

```php?start_inline=true
public function __construct( integer $precision = null ): DateTime
```

##### DateTime constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$precision` | **integer**  |  |



#### ORM\Dbal\Type\DateTime::factory

```php?start_inline=true
public static function factory( $columnDefinition )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` |   |  |



#### ORM\Dbal\Type\DateTime::fromDefinition

```php?start_inline=true
public static function fromDefinition(
    $columnDefinitoin
): \ORM\Dbal\TypeInterface
```

##### Create this type from $columnDefinition.

Returns null when column definition does not match.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinitoin` |   |  |





---

### ORM\Dbal



#### Base class for database abstraction






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$em` | **EntityManager** |  |
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected static** | `$quotingCharacter` | **string** |  |
| **protected static** | `$identifierDivider` | **string** |  |
| **protected static** | `$booleanTrue` | **string** |  |
| **protected static** | `$booleanFalse` | **string** |  |



#### Methods

* [__construct](#ormdbal__construct) Dbal constructor.
* [buildInsertStatement](#ormdbalbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbaldelete) Delete $entity from database
* [describe](#ormdbaldescribe) Describe a table
* [escapeIdentifier](#ormdbalescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormdbalescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalextractparenthesis) Extract content from parenthesis in $type
* [getBooleanFalse](#ormdbalgetbooleanfalse) 
* [getBooleanTrue](#ormdbalgetbooleantrue) 
* [getIdentifierDivider](#ormdbalgetidentifierdivider) 
* [getQuotingCharacter](#ormdbalgetquotingcharacter) 
* [getType](#ormdbalgettype) Get the type for $columnDefinition
* [insert](#ormdbalinsert) Inserts $entity and returns the new ID for autoincrement or true
* [normalizeType](#ormdbalnormalizetype) Normalize $type
* [registerType](#ormdbalregistertype) Register $type for describe
* [setBooleanFalse](#ormdbalsetbooleanfalse) 
* [setBooleanTrue](#ormdbalsetbooleantrue) 
* [setIdentifierDivider](#ormdbalsetidentifierdivider) 
* [setQuotingCharacter](#ormdbalsetquotingcharacter) 

#### ORM\Dbal::__construct

```php?start_inline=true
public function __construct( \ORM\EntityManager $entityManager ): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  |  |



#### ORM\Dbal::buildInsertStatement

```php?start_inline=true
protected function buildInsertStatement( \ORM\Entity $entity ): string
```

##### Build the insert statement for $entity



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |



#### ORM\Dbal::delete

```php?start_inline=true
public function delete( \ORM\Entity $entity ): boolean
```

##### Delete $entity from database

This method does not delete from the map - you can still receive the entity via fetch.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |



#### ORM\Dbal::describe

```php?start_inline=true
public function describe( string $table ): array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />**Throws:** this method may throw **\ORM\Exceptions\UnsupportedDriver**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\Dbal::escapeIdentifier

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



#### ORM\Dbal::escapeValue

```php?start_inline=true
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal::extractParenthesis

```php?start_inline=true
protected function extractParenthesis( string $type ): string
```

##### Extract content from parenthesis in $type



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal::getBooleanFalse

```php?start_inline=true
public static function getBooleanFalse(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal::getBooleanTrue

```php?start_inline=true
public static function getBooleanTrue(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal::getIdentifierDivider

```php?start_inline=true
public static function getIdentifierDivider(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal::getQuotingCharacter

```php?start_inline=true
public static function getQuotingCharacter(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal::getType

```php?start_inline=true
protected function getType( array $columnDefinition ): \ORM\Dbal\TypeInterface
```

##### Get the type for $columnDefinition

Executes fromDefinition of each registered Type

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal::insert

```php?start_inline=true
public function insert(
    \ORM\Entity $entity, boolean $useAutoIncrement = true
): boolean|integer
```

##### Inserts $entity and returns the new ID for autoincrement or true



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|integer**
<br />**Throws:** this method may throw **\ORM\Exceptions\UnsupportedDriver**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |
| `$useAutoIncrement` | **boolean**  |  |



#### ORM\Dbal::normalizeType

```php?start_inline=true
protected function normalizeType( string $type ): string
```

##### Normalize $type

The type returned by mysql is for example VARCHAR(20) - this function converts it to varchar

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal::registerType

```php?start_inline=true
public static function registerType( string $type )
```

##### Register $type for describe



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  | The full qualified class name |



#### ORM\Dbal::setBooleanFalse

```php?start_inline=true
public static function setBooleanFalse( string $false )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$false` | **string**  |  |



#### ORM\Dbal::setBooleanTrue

```php?start_inline=true
public static function setBooleanTrue( string $true )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$true` | **string**  |  |



#### ORM\Dbal::setIdentifierDivider

```php?start_inline=true
public static function setIdentifierDivider( string $divider )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$divider` | **string**  |  |



#### ORM\Dbal::setQuotingCharacter

```php?start_inline=true
public static function setQuotingCharacter( string $char )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |





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

### ORM\Dbal\Type\Double

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Float, double and decimal data type









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


#### Constants

| Name | Value |
|------|-------|
| OPT_RELATION_CLASS | `'class'` |
| OPT_RELATION_CARDINALITY | `'cardinality'` |
| OPT_RELATION_REFERENCE | `'reference'` |
| OPT_RELATION_OPPONENT | `'opponent'` |
| OPT_RELATION_TABLE | `'table'` |


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
| **protected static** | `$relations` | **array** | Relation definitions |
| **protected** | `$data` | **array&lt;mixed>** | The current data of a row. |
| **protected** | `$originalData` | **array&lt;mixed>** | The original data of the row. |
| **protected** | `$entityManager` | **EntityManager** | The entity manager from which this entity got created |
| **protected** | `$relatedObjects` | **array** | Related objects for getRelated |



#### Methods

* [__construct](#ormentity__construct) Constructor
* [__get](#ormentity__get) Get the value from $var
* [__set](#ormentity__set) Set $var to $value
* [addRelated](#ormentityaddrelated) Add relations for $relation to $entities
* [deleteRelated](#ormentitydeleterelated) Delete relations for $relation to $entities
* [describe](#ormentitydescribe) Get an array of Columns for this table.
* [fetch](#ormentityfetch) Fetches related objects
* [forceNamingScheme](#ormentityforcenamingscheme) Enforce $namingScheme to $name
* [getColumnName](#ormentitygetcolumnname) Get the column name of $name
* [getNamingSchemeColumn](#ormentitygetnamingschemecolumn) 
* [getNamingSchemeMethods](#ormentitygetnamingschememethods) 
* [getNamingSchemeTable](#ormentitygetnamingschemetable) 
* [getPrimaryKey](#ormentitygetprimarykey) Get the primary key
* [getPrimaryKeyVars](#ormentitygetprimarykeyvars) Get the primary key vars
* [getReflection](#ormentitygetreflection) Get reflection of the entity
* [getRelated](#ormentitygetrelated) Get related objects
* [getRelation](#ormentitygetrelation) Get the definition for $relation
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
* [setEntityManager](#ormentitysetentitymanager) 
* [setNamingSchemeColumn](#ormentitysetnamingschemecolumn) 
* [setNamingSchemeMethods](#ormentitysetnamingschememethods) 
* [setNamingSchemeTable](#ormentitysetnamingschemetable) 
* [setRelated](#ormentitysetrelated) Set $relation to $entity
* [setTableNameTemplate](#ormentitysettablenametemplate) 
* [unserialize](#ormentityunserialize) Constructs the object

#### ORM\Entity::__construct

```php?start_inline=true
final public function __construct(
    array<mixed> $data = array(), 
    \ORM\EntityManager $entityManager = null, boolean $fromDatabase = false
): Entity
```

##### Constructor

It calls ::onInit() after initializing $data and $originalData.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array&lt;mixed>**  | The current data |
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
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration**<br />

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
**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$var` | **string**  | The variable to change |
| `$value` | **mixed**  | The value to store |



**See Also:**

* [Working with entities](https://tflori.github.io/orm/entities.html)

#### ORM\Entity::addRelated

```php?start_inline=true
public function addRelated(
    string $relation, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager = null
)
```

##### Add relations for $relation to $entities

This method is only for many-to-many relations.

This method does not take care about already existing relations and will fail hard.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\NoEntityManager**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::deleteRelated

```php?start_inline=true
public function deleteRelated(
    string $relation, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager = null
)
```

##### Delete relations for $relation to $entities

This method is only for many-to-many relations.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\NoEntityManager**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::describe

```php?start_inline=true
public static function describe(
    \ORM\EntityManager $entityManager
): array<\ORM\Dbal\Column>
```

##### Get an array of Columns for this table.



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::fetch

```php?start_inline=true
public function fetch(
    string $relation, \ORM\EntityManager $entityManager = null, 
    boolean $getAll = false
): \ORM\Entity|array<\ORM\Entity>|\ORM\EntityFetcher
```

##### Fetches related objects

For relations with cardinality many it returns an EntityFetcher. Otherwise it returns the entity.

It will throw an error for non owner when the key is incomplete.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity|array&lt;mixed,\ORM\Entity&gt;|\ORM\EntityFetcher**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoEntityManager**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  | The relation to fetch |
| `$entityManager` | **EntityManager**  | The EntityManager to use |
| `$getAll` | **boolean**  |  |



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



#### ORM\Entity::getRelated

```php?start_inline=true
public function getRelated( string $relation, boolean $refresh = false ): mixed
```

##### Get related objects

The difference between getRelated and fetch is that getRelated stores the fetched entities. To refresh set
$refresh to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection** or **\ORM\Exceptions\NoEntity** or **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\NoEntityManager** or **\ORM\Exceptions\UndefinedRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$refresh` | **boolean**  |  |



#### ORM\Entity::getRelation

```php?start_inline=true
public static function getRelation( string $relation ): \ORM\Relation
```

##### Get the definition for $relation

It normalize the short definition form and create a Relation object from it.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration** or **\ORM\Exceptions\UndefinedRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |



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

#### ORM\Entity::setEntityManager

```php?start_inline=true
public function setEntityManager( \ORM\EntityManager $entityManager ): Entity
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::setNamingSchemeColumn

```php?start_inline=true
public static function setNamingSchemeColumn( string $namingSchemeColumn )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

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
**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

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



#### ORM\Entity::setRelated

```php?start_inline=true
public function setRelated( string $relation, \ORM\Entity $entity = null )
```

##### Set $relation to $entity

This method is only for the owner of a relation.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey** or **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entity` | **Entity**  |  |



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
| **protected** | `$columns` | **array &#124; null** | Columns to fetch (null is equal to [&#039;*&#039;]) |
| **protected** | `$joins` | **array&lt;string>** | Joins get concatenated with space |
| **protected** | `$limit` | **integer** | Limit amount of rows |
| **protected** | `$offset` | **integer** | Offset to start from |
| **protected** | `$groupBy` | **array&lt;string>** | Group by conditions get concatenated with comma |
| **protected** | `$orderBy` | **array&lt;string>** | Order by conditions get concatenated with comma |
| **protected** | `$modifier` | **array&lt;string>** | Modifiers get concatenated with space |
| **protected** | `$entityManager` | **EntityManager** | EntityManager to use for quoting |
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
* [buildExpression](#ormentityfetcherbuildexpression) 
* [close](#ormentityfetcherclose) Close parenthesis
* [column](#ormentityfetchercolumn) Add $column
* [columns](#ormentityfetchercolumns) Set $columns
* [convertPlaceholders](#ormentityfetcherconvertplaceholders) Replaces questionmarks in $expression with $args
* [count](#ormentityfetchercount) 
* [createRelatedJoin](#ormentityfetchercreaterelatedjoin) Create the join with $join type
* [fullJoin](#ormentityfetcherfulljoin) Full (outer) join $tableName with $options
* [getDefaultOperator](#ormentityfetchergetdefaultoperator) 
* [getEntityManager](#ormentityfetchergetentitymanager) 
* [getExpression](#ormentityfetchergetexpression) Get the expression
* [getQuery](#ormentityfetchergetquery) Get the query / select statement
* [getStatement](#ormentityfetchergetstatement) Query database and return result
* [groupBy](#ormentityfetchergroupby) Group By $column
* [join](#ormentityfetcherjoin) (Inner) join $tableName with $options
* [joinRelated](#ormentityfetcherjoinrelated) Join $relation
* [leftJoin](#ormentityfetcherleftjoin) Left (outer) join $tableName with $options
* [leftJoinRelated](#ormentityfetcherleftjoinrelated) Left outer join $relation
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
    string $column, string $operator = null, string $value = null
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



#### ORM\EntityFetcher::buildExpression

```php?start_inline=true
private function buildExpression( $column, $value, $operator = null )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` |   |  |
| `$value` |   |  |
| `$operator` |   |  |



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



#### ORM\EntityFetcher::count

```php?start_inline=true
public function count()
```




**Visibility:** this method is **public**.
<br />




#### ORM\EntityFetcher::createRelatedJoin

```php?start_inline=true
public function createRelatedJoin( $join, $relation ): $this
```

##### Create the join with $join type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$join` |   |  |
| `$relation` |   |  |



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



#### ORM\EntityFetcher::getDefaultOperator

```php?start_inline=true
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\EntityFetcher::getEntityManager

```php?start_inline=true
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



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



#### ORM\EntityFetcher::joinRelated

```php?start_inline=true
public function joinRelated( $relation ): $this
```

##### Join $relation



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` |   |  |



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



#### ORM\EntityFetcher::leftJoinRelated

```php?start_inline=true
public function leftJoinRelated( $relation ): $this
```

##### Left outer join $relation



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` |   |  |



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
    string $column, string $operator = null, string $value = null
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
    string $column, string $operator = null, string $value = null
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
| OPT_TABLE_NAME_TEMPLATE | `'tableNameTemplate'` |
| OPT_NAMING_SCHEME_TABLE | `'namingSchemeTable'` |
| OPT_NAMING_SCHEME_COLUMN | `'namingSchemeColumn'` |
| OPT_NAMING_SCHEME_METHODS | `'namingSchemeMethods'` |
| OPT_MYSQL_BOOLEAN_TRUE | `'mysqlTrue'` |
| OPT_MYSQL_BOOLEAN_FALSE | `'mysqlFalse'` |
| OPT_SQLITE_BOOLEAN_TRUE | `'sqliteTrue'` |
| OPT_SQLITE_BOOLEAN_FASLE | `'sqliteFalse'` |
| OPT_PGSQL_BOOLEAN_TRUE | `'pgsqlTrue'` |
| OPT_PGSQL_BOOLEAN_FALSE | `'pgsqlFalse'` |
| OPT_QUOTING_CHARACTER | `'quotingChar'` |
| OPT_IDENTIFIER_DIVIDER | `'identifierDivider'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$connection` | ** \ PDO &#124; callable &#124; DbConfig** | Connection to database |
| **private** | `$dbal` | **Dbal** | The Database Abstraction Layer |
| **protected** | `$map` | **array&lt;Entity[]>** | The Entity map |
| **protected** | `$options` | **array** | The options set for this instance |
| **protected** | `$descriptions` | **array&lt;Dbal \ Column[]>** | Already fetched column descriptions |



#### Methods

* [__construct](#ormentitymanager__construct) Constructor
* [delete](#ormentitymanagerdelete) Delete $entity from database
* [describe](#ormentitymanagerdescribe) Returns an array of columns from $table.
* [escapeIdentifier](#ormentitymanagerescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormentitymanagerescapevalue) Returns $value formatted to use in a sql statement.
* [fetch](#ormentitymanagerfetch) Fetch one or more entities
* [getConnection](#ormentitymanagergetconnection) Get the pdo connection.
* [getDbal](#ormentitymanagergetdbal) 
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
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |



#### ORM\EntityManager::describe

```php?start_inline=true
public function describe( string $table ): array<\ORM\Dbal\Column>
```

##### Returns an array of columns from $table.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



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
<br />

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

##### Get the pdo connection.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\PDO**
<br />**Throws:** this method may throw **\ORM\Exceptions\NoConnection**<br />



#### ORM\EntityManager::getDbal

```php?start_inline=true
public function getDbal()
```




**Visibility:** this method is **public**.
<br />




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

### ORM\Dbal\Type\Enum

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Enum data type









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

### ORM\Dbal\Type\Integer

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Integer (of any size) data type









---

### ORM\Exceptions\InvalidConfiguration

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\InvalidName

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\InvalidRelation

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Type\Json

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Json data type









---

### ORM\Relation\ManyToMany

**Extends:** [ORM\Relation](#ormrelation)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** | The name of the relation for error messages |
| **protected** | `$class` | **string** | The class that is related |
| **protected** | `$opponent` | **string** | The name of the relation in the related class |
| **protected** | `$reference` | **array** | Reference definition as key value pairs |
| **protected** | `$table` | **string'categories** | The table that holds the foreign keys |



#### Methods

* [__construct](#ormrelationmanytomany__construct) ManyToMany constructor.
* [addJoin](#ormrelationmanytomanyaddjoin) Join this relation in $fetcher
* [addRelated](#ormrelationmanytomanyaddrelated) Add $entities to association table
* [convertShort](#ormrelationmanytomanyconvertshort) Converts short form to assoc form
* [createRelation](#ormrelationmanytomanycreaterelation) Factory for relation definition object
* [deleteRelated](#ormrelationmanytomanydeleterelated) Delete $entities from association table
* [fetch](#ormrelationmanytomanyfetch) Fetch the relation
* [fetchAll](#ormrelationmanytomanyfetchall) Fetch all from the relation
* [getClass](#ormrelationmanytomanygetclass) 
* [getForeignKey](#ormrelationmanytomanygetforeignkey) Get the foreign key for the given reference
* [getOpponent](#ormrelationmanytomanygetopponent) 
* [getReference](#ormrelationmanytomanygetreference) 
* [getTable](#ormrelationmanytomanygettable) 
* [setRelated](#ormrelationmanytomanysetrelated) Set the relation to $entity

#### ORM\Relation\ManyToMany::__construct

```php?start_inline=true
public function __construct(
    string $name, string $class, array $reference, string $opponent, 
    string $table
): ManyToMany
```

##### ManyToMany constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$class` | **string**  |  |
| `$reference` | **array**  |  |
| `$opponent` | **string**  |  |
| `$table` | **string**  |  |



#### ORM\Relation\ManyToMany::addJoin

```php?start_inline=true
public function addJoin(
    \ORM\EntityFetcher $fetcher, string $join, string $alias
): mixed
```

##### Join this relation in $fetcher



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **\ORM\EntityFetcher**  |  |
| `$join` | **string**  |  |
| `$alias` | **string**  |  |



#### ORM\Relation\ManyToMany::addRelated

```php?start_inline=true
public function addRelated(
    \ORM\Entity $me, array $entities, \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::convertShort

```php?start_inline=true
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\ManyToMany::createRelation

```php?start_inline=true
public static function createRelation(
    string $name, array $relDef
): \ORM\Relation
```

##### Factory for relation definition object



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\ManyToMany::deleteRelated

```php?start_inline=true
public function deleteRelated(
    \ORM\Entity $me, array $entities, \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::fetch

```php?start_inline=true
public function fetch(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::fetchAll

```php?start_inline=true
public function fetchAll(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): array<\ORM\Entity>|\ORM\Entity
```

##### Fetch all from the relation

Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;|\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::getClass

```php?start_inline=true
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\ManyToMany::getForeignKey

```php?start_inline=true
protected function getForeignKey( \ORM\Entity $me, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\ManyToMany::getOpponent

```php?start_inline=true
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\ManyToMany::getReference

```php?start_inline=true
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\ManyToMany::getTable

```php?start_inline=true
public function getTable(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\ManyToMany::setRelated

```php?start_inline=true
public function setRelated( \ORM\Entity $me, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Dbal\Mysql

**Extends:** [ORM\Dbal](#ormdbal)


#### Database abstraction for MySQL databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$em` | ** \ ORM \ EntityManager** |  |
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected static** | `$quotingCharacter` | **string** |  |
| **protected static** | `$identifierDivider` | **string** |  |
| **protected static** | `$booleanTrue` | **string** |  |
| **protected static** | `$booleanFalse` | **string** |  |
| **protected static** | `$typeMapping` |  |  |



#### Methods

* [__construct](#ormdbalmysql__construct) Dbal constructor.
* [buildInsertStatement](#ormdbalmysqlbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalmysqldelete) Delete $entity from database
* [describe](#ormdbalmysqldescribe) Describe a table
* [escapeIdentifier](#ormdbalmysqlescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormdbalmysqlescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalmysqlextractparenthesis) Extract content from parenthesis in $type
* [getBooleanFalse](#ormdbalmysqlgetbooleanfalse) 
* [getBooleanTrue](#ormdbalmysqlgetbooleantrue) 
* [getIdentifierDivider](#ormdbalmysqlgetidentifierdivider) 
* [getQuotingCharacter](#ormdbalmysqlgetquotingcharacter) 
* [getType](#ormdbalmysqlgettype) Get the type for $columnDefinition
* [insert](#ormdbalmysqlinsert) Inserts $entity and returns the new ID for autoincrement or true
* [normalizeColumnDefinition](#ormdbalmysqlnormalizecolumndefinition) Normalize a column definition
* [normalizeType](#ormdbalmysqlnormalizetype) Normalize $type
* [registerType](#ormdbalmysqlregistertype) Register $type for describe
* [setBooleanFalse](#ormdbalmysqlsetbooleanfalse) 
* [setBooleanTrue](#ormdbalmysqlsetbooleantrue) 
* [setIdentifierDivider](#ormdbalmysqlsetidentifierdivider) 
* [setQuotingCharacter](#ormdbalmysqlsetquotingcharacter) 

#### ORM\Dbal\Mysql::__construct

```php?start_inline=true
public function __construct( \ORM\EntityManager $entityManager ): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Dbal\Mysql::buildInsertStatement

```php?start_inline=true
protected function buildInsertStatement( \ORM\Entity $entity ): string
```

##### Build the insert statement for $entity



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::delete

```php?start_inline=true
public function delete( \ORM\Entity $entity ): boolean
```

##### Delete $entity from database

This method does not delete from the map - you can still receive the entity via fetch.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::describe

```php?start_inline=true
public function describe( string $table ): array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\Dbal\Mysql::escapeIdentifier

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



#### ORM\Dbal\Mysql::escapeValue

```php?start_inline=true
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Mysql::extractParenthesis

```php?start_inline=true
protected function extractParenthesis( string $type ): string
```

##### Extract content from parenthesis in $type



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Mysql::getBooleanFalse

```php?start_inline=true
public static function getBooleanFalse(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Mysql::getBooleanTrue

```php?start_inline=true
public static function getBooleanTrue(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Mysql::getIdentifierDivider

```php?start_inline=true
public static function getIdentifierDivider(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Mysql::getQuotingCharacter

```php?start_inline=true
public static function getQuotingCharacter(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Mysql::getType

```php?start_inline=true
protected function getType( array $columnDefinition ): \ORM\Dbal\TypeInterface
```

##### Get the type for $columnDefinition

Executes fromDefinition of each registered Type

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Mysql::insert

```php?start_inline=true
public function insert(
    \ORM\Entity $entity, boolean $useAutoIncrement = true
): boolean|integer
```

##### Inserts $entity and returns the new ID for autoincrement or true



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|integer**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$useAutoIncrement` | **boolean**  |  |



#### ORM\Dbal\Mysql::normalizeColumnDefinition

```php?start_inline=true
protected function normalizeColumnDefinition( array $rawColumn ): array
```

##### Normalize a column definition

The column definition from "DESCRIBE <table>" is to special as useful. Here we normalize it to a more
ANSI-SQL style.

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rawColumn` | **array**  |  |



#### ORM\Dbal\Mysql::normalizeType

```php?start_inline=true
protected function normalizeType( string $type ): string
```

##### Normalize $type

The type returned by mysql is for example VARCHAR(20) - this function converts it to varchar

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Mysql::registerType

```php?start_inline=true
public static function registerType( string $type )
```

##### Register $type for describe



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  | The full qualified class name |



#### ORM\Dbal\Mysql::setBooleanFalse

```php?start_inline=true
public static function setBooleanFalse( string $false )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$false` | **string**  |  |



#### ORM\Dbal\Mysql::setBooleanTrue

```php?start_inline=true
public static function setBooleanTrue( string $true )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$true` | **string**  |  |



#### ORM\Dbal\Mysql::setIdentifierDivider

```php?start_inline=true
public static function setIdentifierDivider( string $divider )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$divider` | **string**  |  |



#### ORM\Dbal\Mysql::setQuotingCharacter

```php?start_inline=true
public static function setQuotingCharacter( string $char )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |





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

### ORM\Exceptions\NoOperator

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

### ORM\Relation\OneToMany

**Extends:** [ORM\Relation](#ormrelation)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** | The name of the relation for error messages |
| **protected** | `$class` | **string** | The class that is related |
| **protected** | `$opponent` | **string** | The name of the relation in the related class |
| **protected** | `$reference` | **array** | Reference definition as key value pairs |



#### Methods

* [__construct](#ormrelationonetomany__construct) Owner constructor.
* [addJoin](#ormrelationonetomanyaddjoin) Join this relation in $fetcher
* [addRelated](#ormrelationonetomanyaddrelated) Add $entities to association table
* [convertShort](#ormrelationonetomanyconvertshort) Converts short form to assoc form
* [createRelation](#ormrelationonetomanycreaterelation) Factory for relation definition object
* [deleteRelated](#ormrelationonetomanydeleterelated) Delete $entities from association table
* [fetch](#ormrelationonetomanyfetch) Fetch the relation
* [fetchAll](#ormrelationonetomanyfetchall) Fetch all from the relation
* [getClass](#ormrelationonetomanygetclass) 
* [getForeignKey](#ormrelationonetomanygetforeignkey) Get the foreign key for the given reference
* [getOpponent](#ormrelationonetomanygetopponent) 
* [getReference](#ormrelationonetomanygetreference) 
* [setRelated](#ormrelationonetomanysetrelated) Set the relation to $entity

#### ORM\Relation\OneToMany::__construct

```php?start_inline=true
public function __construct(
    string $name, string $class, string $opponent
): OneToMany
```

##### Owner constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$class` | **string**  |  |
| `$opponent` | **string**  |  |



#### ORM\Relation\OneToMany::addJoin

```php?start_inline=true
public function addJoin(
    \ORM\EntityFetcher $fetcher, string $join, string $alias
): mixed
```

##### Join this relation in $fetcher



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **\ORM\EntityFetcher**  |  |
| `$join` | **string**  |  |
| `$alias` | **string**  |  |



#### ORM\Relation\OneToMany::addRelated

```php?start_inline=true
public function addRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::convertShort

```php?start_inline=true
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToMany::createRelation

```php?start_inline=true
public static function createRelation(
    string $name, array $relDef
): \ORM\Relation
```

##### Factory for relation definition object



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToMany::deleteRelated

```php?start_inline=true
public function deleteRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::fetch

```php?start_inline=true
public function fetch(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::fetchAll

```php?start_inline=true
public function fetchAll(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): array<\ORM\Entity>|\ORM\Entity
```

##### Fetch all from the relation

Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;|\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::getClass

```php?start_inline=true
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\OneToMany::getForeignKey

```php?start_inline=true
protected function getForeignKey( \ORM\Entity $me, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\OneToMany::getOpponent

```php?start_inline=true
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\OneToMany::getReference

```php?start_inline=true
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\OneToMany::setRelated

```php?start_inline=true
public function setRelated( \ORM\Entity $me, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Relation\OneToOne

**Extends:** [ORM\Relation\OneToMany](#ormrelationonetomany)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** | The name of the relation for error messages |
| **protected** | `$class` | **string** | The class that is related |
| **protected** | `$opponent` | **string** | The name of the relation in the related class |
| **protected** | `$reference` | **array** | Reference definition as key value pairs |



#### Methods

* [__construct](#ormrelationonetoone__construct) Owner constructor.
* [addJoin](#ormrelationonetooneaddjoin) Join this relation in $fetcher
* [addRelated](#ormrelationonetooneaddrelated) Add $entities to association table
* [convertShort](#ormrelationonetooneconvertshort) Converts short form to assoc form
* [createRelation](#ormrelationonetoonecreaterelation) Factory for relation definition object
* [deleteRelated](#ormrelationonetoonedeleterelated) Delete $entities from association table
* [fetch](#ormrelationonetoonefetch) Fetch the relation
* [fetchAll](#ormrelationonetoonefetchall) Fetch all from the relation
* [getClass](#ormrelationonetoonegetclass) 
* [getForeignKey](#ormrelationonetoonegetforeignkey) Get the foreign key for the given reference
* [getOpponent](#ormrelationonetoonegetopponent) 
* [getReference](#ormrelationonetoonegetreference) 
* [setRelated](#ormrelationonetoonesetrelated) Set the relation to $entity

#### ORM\Relation\OneToOne::__construct

```php?start_inline=true
public function __construct(
    string $name, string $class, string $opponent
): OneToMany
```

##### Owner constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$class` | **string**  |  |
| `$opponent` | **string**  |  |



#### ORM\Relation\OneToOne::addJoin

```php?start_inline=true
abstract public function addJoin(
    \ORM\EntityFetcher $fetcher, string $join, string $alias
): mixed
```

##### Join this relation in $fetcher



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **\ORM\EntityFetcher**  |  |
| `$join` | **string**  |  |
| `$alias` | **string**  |  |



#### ORM\Relation\OneToOne::addRelated

```php?start_inline=true
public function addRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::convertShort

```php?start_inline=true
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToOne::createRelation

```php?start_inline=true
public static function createRelation(
    string $name, array $relDef
): \ORM\Relation
```

##### Factory for relation definition object



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToOne::deleteRelated

```php?start_inline=true
public function deleteRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::fetch

```php?start_inline=true
public function fetch(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::fetchAll

```php?start_inline=true
public function fetchAll(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): array<\ORM\Entity>|\ORM\Entity
```

##### Fetch all from the relation

Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;|\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::getClass

```php?start_inline=true
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\OneToOne::getForeignKey

```php?start_inline=true
protected function getForeignKey( \ORM\Entity $me, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\OneToOne::getOpponent

```php?start_inline=true
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\OneToOne::getReference

```php?start_inline=true
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\OneToOne::setRelated

```php?start_inline=true
public function setRelated( \ORM\Entity $me, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Dbal\Other

**Extends:** [ORM\Dbal](#ormdbal)


#### Database abstraction for other databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$em` | ** \ ORM \ EntityManager** |  |
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected static** | `$quotingCharacter` | **string** |  |
| **protected static** | `$identifierDivider` | **string** |  |
| **protected static** | `$booleanTrue` | **string** |  |
| **protected static** | `$booleanFalse` | **string** |  |




---

### ORM\Relation\Owner

**Extends:** [ORM\Relation](#ormrelation)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** | The name of the relation for error messages |
| **protected** | `$class` | **string** | The class that is related |
| **protected** | `$opponent` | **string** | The name of the relation in the related class |
| **protected** | `$reference` | **array** | Reference definition as key value pairs |



#### Methods

* [__construct](#ormrelationowner__construct) Owner constructor.
* [addJoin](#ormrelationowneraddjoin) Join this relation in $fetcher
* [addRelated](#ormrelationowneraddrelated) Add $entities to association table
* [convertShort](#ormrelationownerconvertshort) Converts short form to assoc form
* [createRelation](#ormrelationownercreaterelation) Factory for relation definition object
* [deleteRelated](#ormrelationownerdeleterelated) Delete $entities from association table
* [fetch](#ormrelationownerfetch) Fetch the relation
* [fetchAll](#ormrelationownerfetchall) Fetch all from the relation
* [getClass](#ormrelationownergetclass) 
* [getForeignKey](#ormrelationownergetforeignkey) Get the foreign key for the given reference
* [getOpponent](#ormrelationownergetopponent) 
* [getReference](#ormrelationownergetreference) 
* [setRelated](#ormrelationownersetrelated) Set the relation to $entity

#### ORM\Relation\Owner::__construct

```php?start_inline=true
public function __construct(
    string $name, string $class, array $reference
): Owner
```

##### Owner constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$class` | **string**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\Owner::addJoin

```php?start_inline=true
public function addJoin(
    \ORM\EntityFetcher $fetcher, string $join, string $alias
): mixed
```

##### Join this relation in $fetcher



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **\ORM\EntityFetcher**  |  |
| `$join` | **string**  |  |
| `$alias` | **string**  |  |



#### ORM\Relation\Owner::addRelated

```php?start_inline=true
public function addRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::convertShort

```php?start_inline=true
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\Owner::createRelation

```php?start_inline=true
public static function createRelation(
    string $name, array $relDef
): \ORM\Relation
```

##### Factory for relation definition object



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\Owner::deleteRelated

```php?start_inline=true
public function deleteRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::fetch

```php?start_inline=true
public function fetch(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::fetchAll

```php?start_inline=true
public function fetchAll(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): array<\ORM\Entity>|\ORM\Entity
```

##### Fetch all from the relation

Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;|\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::getClass

```php?start_inline=true
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\Owner::getForeignKey

```php?start_inline=true
protected function getForeignKey( \ORM\Entity $me, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\Owner::getOpponent

```php?start_inline=true
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\Owner::getReference

```php?start_inline=true
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\Owner::setRelated

```php?start_inline=true
public function setRelated( \ORM\Entity $me, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity**  |  |





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
    string $column, string $operator = null, string $value = null
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
    string $column, string $operator = null, string $value = null
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
    string $column, string $operator = null, string $value = null
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

### ORM\Dbal\Pgsql

**Extends:** [ORM\Dbal](#ormdbal)


#### Database abstraction for PostgreSQL databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$em` | ** \ ORM \ EntityManager** |  |
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected static** | `$quotingCharacter` | **string** |  |
| **protected static** | `$identifierDivider` | **string** |  |
| **protected static** | `$booleanTrue` | **string** |  |
| **protected static** | `$booleanFalse` | **string** |  |
| **protected static** | `$typeMapping` |  |  |



#### Methods

* [__construct](#ormdbalpgsql__construct) Dbal constructor.
* [buildInsertStatement](#ormdbalpgsqlbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalpgsqldelete) Delete $entity from database
* [describe](#ormdbalpgsqldescribe) Describe a table
* [escapeIdentifier](#ormdbalpgsqlescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormdbalpgsqlescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalpgsqlextractparenthesis) Extract content from parenthesis in $type
* [getBooleanFalse](#ormdbalpgsqlgetbooleanfalse) 
* [getBooleanTrue](#ormdbalpgsqlgetbooleantrue) 
* [getIdentifierDivider](#ormdbalpgsqlgetidentifierdivider) 
* [getQuotingCharacter](#ormdbalpgsqlgetquotingcharacter) 
* [getType](#ormdbalpgsqlgettype) Get the type for $columnDefinition
* [insert](#ormdbalpgsqlinsert) Inserts $entity and returns the new ID for autoincrement or true
* [normalizeType](#ormdbalpgsqlnormalizetype) Normalize $type
* [registerType](#ormdbalpgsqlregistertype) Register $type for describe
* [setBooleanFalse](#ormdbalpgsqlsetbooleanfalse) 
* [setBooleanTrue](#ormdbalpgsqlsetbooleantrue) 
* [setIdentifierDivider](#ormdbalpgsqlsetidentifierdivider) 
* [setQuotingCharacter](#ormdbalpgsqlsetquotingcharacter) 

#### ORM\Dbal\Pgsql::__construct

```php?start_inline=true
public function __construct( \ORM\EntityManager $entityManager ): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Dbal\Pgsql::buildInsertStatement

```php?start_inline=true
protected function buildInsertStatement( \ORM\Entity $entity ): string
```

##### Build the insert statement for $entity



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::delete

```php?start_inline=true
public function delete( \ORM\Entity $entity ): boolean
```

##### Delete $entity from database

This method does not delete from the map - you can still receive the entity via fetch.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::describe

```php?start_inline=true
public function describe( $schemaTable ): array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$schemaTable` |   |  |



#### ORM\Dbal\Pgsql::escapeIdentifier

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



#### ORM\Dbal\Pgsql::escapeValue

```php?start_inline=true
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Pgsql::extractParenthesis

```php?start_inline=true
protected function extractParenthesis( string $type ): string
```

##### Extract content from parenthesis in $type



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Pgsql::getBooleanFalse

```php?start_inline=true
public static function getBooleanFalse(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Pgsql::getBooleanTrue

```php?start_inline=true
public static function getBooleanTrue(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Pgsql::getIdentifierDivider

```php?start_inline=true
public static function getIdentifierDivider(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Pgsql::getQuotingCharacter

```php?start_inline=true
public static function getQuotingCharacter(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Pgsql::getType

```php?start_inline=true
protected function getType( array $columnDefinition ): \ORM\Dbal\TypeInterface
```

##### Get the type for $columnDefinition

Executes fromDefinition of each registered Type

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Pgsql::insert

```php?start_inline=true
public function insert(
    \ORM\Entity $entity, boolean $useAutoIncrement = true
): boolean|integer
```

##### Inserts $entity and returns the new ID for autoincrement or true



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|integer**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$useAutoIncrement` | **boolean**  |  |



#### ORM\Dbal\Pgsql::normalizeType

```php?start_inline=true
protected function normalizeType( string $type ): string
```

##### Normalize $type

The type returned by mysql is for example VARCHAR(20) - this function converts it to varchar

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Pgsql::registerType

```php?start_inline=true
public static function registerType( string $type )
```

##### Register $type for describe



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  | The full qualified class name |



#### ORM\Dbal\Pgsql::setBooleanFalse

```php?start_inline=true
public static function setBooleanFalse( string $false )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$false` | **string**  |  |



#### ORM\Dbal\Pgsql::setBooleanTrue

```php?start_inline=true
public static function setBooleanTrue( string $true )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$true` | **string**  |  |



#### ORM\Dbal\Pgsql::setIdentifierDivider

```php?start_inline=true
public static function setIdentifierDivider( string $divider )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$divider` | **string**  |  |



#### ORM\Dbal\Pgsql::setQuotingCharacter

```php?start_inline=true
public static function setQuotingCharacter( string $char )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |





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
| **protected** | `$columns` | **array &#124; null** | Columns to fetch (null is equal to [&#039;*&#039;]) |
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
* [buildExpression](#ormquerybuilderquerybuilderbuildexpression) 
* [close](#ormquerybuilderquerybuilderclose) Close parenthesis
* [column](#ormquerybuilderquerybuildercolumn) Add $column
* [columns](#ormquerybuilderquerybuildercolumns) Set $columns
* [convertPlaceholders](#ormquerybuilderquerybuilderconvertplaceholders) Replaces question marks in $expression with $args
* [fullJoin](#ormquerybuilderquerybuilderfulljoin) Full (outer) join $tableName with $options
* [getDefaultOperator](#ormquerybuilderquerybuildergetdefaultoperator) 
* [getEntityManager](#ormquerybuilderquerybuildergetentitymanager) 
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
    string $column, string $operator = null, string $value = null
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



#### ORM\QueryBuilder\QueryBuilder::buildExpression

```php?start_inline=true
private function buildExpression( $column, $value, $operator = null )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` |   |  |
| `$value` |   |  |
| `$operator` |   |  |



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



#### ORM\QueryBuilder\QueryBuilder::getDefaultOperator

```php?start_inline=true
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\QueryBuilder\QueryBuilder::getEntityManager

```php?start_inline=true
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



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
    string $column, string $operator = null, string $value = null
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
    string $column, string $operator = null, string $value = null
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

### ORM\Relation








#### Constants

| Name | Value |
|------|-------|
| CARDINALITY_ONE | `'one'` |
| CARDINALITY_MANY | `'many'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$name` | **string** | The name of the relation for error messages |
| **protected** | `$class` | **string** | The class that is related |
| **protected** | `$opponent` | **string** | The name of the relation in the related class |
| **protected** | `$reference` | **array** | Reference definition as key value pairs |



#### Methods

* [addJoin](#ormrelationaddjoin) Join this relation in $fetcher
* [addRelated](#ormrelationaddrelated) Add $entities to association table
* [convertShort](#ormrelationconvertshort) Converts short form to assoc form
* [createRelation](#ormrelationcreaterelation) Factory for relation definition object
* [deleteRelated](#ormrelationdeleterelated) Delete $entities from association table
* [fetch](#ormrelationfetch) Fetch the relation
* [fetchAll](#ormrelationfetchall) Fetch all from the relation
* [getClass](#ormrelationgetclass) 
* [getForeignKey](#ormrelationgetforeignkey) Get the foreign key for the given reference
* [getOpponent](#ormrelationgetopponent) 
* [getReference](#ormrelationgetreference) 
* [setRelated](#ormrelationsetrelated) Set the relation to $entity

#### ORM\Relation::addJoin

```php?start_inline=true
abstract public function addJoin(
    \ORM\EntityFetcher $fetcher, string $join, string $alias
): mixed
```

##### Join this relation in $fetcher



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **EntityFetcher**  |  |
| `$join` | **string**  |  |
| `$alias` | **string**  |  |



#### ORM\Relation::addRelated

```php?start_inline=true
public function addRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::convertShort

```php?start_inline=true
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation::createRelation

```php?start_inline=true
public static function createRelation(
    string $name, array $relDef
): \ORM\Relation
```

##### Factory for relation definition object



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation::deleteRelated

```php?start_inline=true
public function deleteRelated(
    \ORM\Entity $me, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::fetch

```php?start_inline=true
abstract public function fetch(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::fetchAll

```php?start_inline=true
public function fetchAll(
    \ORM\Entity $me, \ORM\EntityManager $entityManager
): array<\ORM\Entity>|\ORM\Entity
```

##### Fetch all from the relation

Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;|\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::getClass

```php?start_inline=true
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation::getForeignKey

```php?start_inline=true
protected function getForeignKey( \ORM\Entity $me, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exceptions\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation::getOpponent

```php?start_inline=true
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation::getReference

```php?start_inline=true
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation::setRelated

```php?start_inline=true
public function setRelated( \ORM\Entity $me, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exceptions\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$me` | **Entity**  |  |
| `$entity` | **Entity &#124; null**  |  |





---

### ORM\Dbal\Type\Set

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Set data type









---

### ORM\Dbal\Sqlite

**Extends:** [ORM\Dbal](#ormdbal)


#### Database abstraction for SQLite databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$em` | ** \ ORM \ EntityManager** |  |
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected static** | `$quotingCharacter` | **string** |  |
| **protected static** | `$identifierDivider` | **string** |  |
| **protected static** | `$booleanTrue` | **string** |  |
| **protected static** | `$booleanFalse` | **string** |  |
| **protected static** | `$typeMapping` |  |  |



#### Methods

* [__construct](#ormdbalsqlite__construct) Dbal constructor.
* [buildInsertStatement](#ormdbalsqlitebuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalsqlitedelete) Delete $entity from database
* [describe](#ormdbalsqlitedescribe) Describe a table
* [escapeIdentifier](#ormdbalsqliteescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormdbalsqliteescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalsqliteextractparenthesis) Extract content from parenthesis in $type
* [getBooleanFalse](#ormdbalsqlitegetbooleanfalse) 
* [getBooleanTrue](#ormdbalsqlitegetbooleantrue) 
* [getIdentifierDivider](#ormdbalsqlitegetidentifierdivider) 
* [getQuotingCharacter](#ormdbalsqlitegetquotingcharacter) 
* [getType](#ormdbalsqlitegettype) Get the type for $columnDefinition
* [hasMultiplePrimaryKey](#ormdbalsqlitehasmultipleprimarykey) Checks $rawColumns for a multiple primary key
* [insert](#ormdbalsqliteinsert) Inserts $entity and returns the new ID for autoincrement or true
* [normalizeColumnDefinition](#ormdbalsqlitenormalizecolumndefinition) Normalize a column definition
* [normalizeType](#ormdbalsqlitenormalizetype) Normalize $type
* [registerType](#ormdbalsqliteregistertype) Register $type for describe
* [setBooleanFalse](#ormdbalsqlitesetbooleanfalse) 
* [setBooleanTrue](#ormdbalsqlitesetbooleantrue) 
* [setIdentifierDivider](#ormdbalsqlitesetidentifierdivider) 
* [setQuotingCharacter](#ormdbalsqlitesetquotingcharacter) 

#### ORM\Dbal\Sqlite::__construct

```php?start_inline=true
public function __construct( \ORM\EntityManager $entityManager ): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Dbal\Sqlite::buildInsertStatement

```php?start_inline=true
protected function buildInsertStatement( \ORM\Entity $entity ): string
```

##### Build the insert statement for $entity



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::delete

```php?start_inline=true
public function delete( \ORM\Entity $entity ): boolean
```

##### Delete $entity from database

This method does not delete from the map - you can still receive the entity via fetch.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::describe

```php?start_inline=true
public function describe( $schemaTable ): array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$schemaTable` |   |  |



#### ORM\Dbal\Sqlite::escapeIdentifier

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



#### ORM\Dbal\Sqlite::escapeValue

```php?start_inline=true
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exceptions\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Sqlite::extractParenthesis

```php?start_inline=true
protected function extractParenthesis( string $type ): string
```

##### Extract content from parenthesis in $type



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Sqlite::getBooleanFalse

```php?start_inline=true
public static function getBooleanFalse(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Sqlite::getBooleanTrue

```php?start_inline=true
public static function getBooleanTrue(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Sqlite::getIdentifierDivider

```php?start_inline=true
public static function getIdentifierDivider(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Sqlite::getQuotingCharacter

```php?start_inline=true
public static function getQuotingCharacter(): string
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Sqlite::getType

```php?start_inline=true
protected function getType( array $columnDefinition ): \ORM\Dbal\TypeInterface
```

##### Get the type for $columnDefinition

Executes fromDefinition of each registered Type

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Sqlite::hasMultiplePrimaryKey

```php?start_inline=true
protected function hasMultiplePrimaryKey( array $rawColumns ): boolean
```

##### Checks $rawColumns for a multiple primary key



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rawColumns` | **array**  |  |



#### ORM\Dbal\Sqlite::insert

```php?start_inline=true
public function insert(
    \ORM\Entity $entity, boolean $useAutoIncrement = true
): boolean|integer
```

##### Inserts $entity and returns the new ID for autoincrement or true



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|integer**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$useAutoIncrement` | **boolean**  |  |



#### ORM\Dbal\Sqlite::normalizeColumnDefinition

```php?start_inline=true
protected function normalizeColumnDefinition(
    array $rawColumn, $hasMultiplePrimaryKey = false
): array
```

##### Normalize a column definition

The column definition from "PRAGMA table_info(<table>)" is to special as useful. Here we normalize it to a more
ANSI-SQL style.

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rawColumn` | **array**  |  |
| `$hasMultiplePrimaryKey` |   |  |



#### ORM\Dbal\Sqlite::normalizeType

```php?start_inline=true
protected function normalizeType( string $type ): string
```

##### Normalize $type

The type returned by mysql is for example VARCHAR(20) - this function converts it to varchar

**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  |  |



#### ORM\Dbal\Sqlite::registerType

```php?start_inline=true
public static function registerType( string $type )
```

##### Register $type for describe



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | **string**  | The full qualified class name |



#### ORM\Dbal\Sqlite::setBooleanFalse

```php?start_inline=true
public static function setBooleanFalse( string $false )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$false` | **string**  |  |



#### ORM\Dbal\Sqlite::setBooleanTrue

```php?start_inline=true
public static function setBooleanTrue( string $true )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$true` | **string**  |  |



#### ORM\Dbal\Sqlite::setIdentifierDivider

```php?start_inline=true
public static function setIdentifierDivider( string $divider )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$divider` | **string**  |  |



#### ORM\Dbal\Sqlite::setQuotingCharacter

```php?start_inline=true
public static function setQuotingCharacter( string $char )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$char` | **string**  |  |





---

### ORM\Dbal\Type\Text

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Text data type

This is also the base type for any other data type







---

### ORM\Dbal\Type\Time

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Time data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$precision` | **integer** |  |



#### Methods

* [__construct](#ormdbaltypetime__construct) DateTime constructor.
* [factory](#ormdbaltypetimefactory) 
* [fromDefinition](#ormdbaltypetimefromdefinition) Create this type from $columnDefinition.

#### ORM\Dbal\Type\Time::__construct

```php?start_inline=true
public function __construct( integer $precision = null ): Time
```

##### DateTime constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$precision` | **integer**  |  |



#### ORM\Dbal\Type\Time::factory

```php?start_inline=true
public static function factory( $columnDefinition )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` |   |  |



#### ORM\Dbal\Type\Time::fromDefinition

```php?start_inline=true
public static function fromDefinition(
    $columnDefinitoin
): \ORM\Dbal\TypeInterface
```

##### Create this type from $columnDefinition.

Returns null when column definition does not match.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinitoin` |   |  |





---

### ORM\Dbal\Type


**Implements:** [ORM\Dbal\TypeInterface](#ormdbaltypeinterface)

#### Base class for data types








#### Methods

* [fromDefinition](#ormdbaltypefromdefinition) Create this type from $columnDefinition.

#### ORM\Dbal\Type::fromDefinition

```php?start_inline=true
public static function fromDefinition(
    $columnDefinitoin
): \ORM\Dbal\TypeInterface
```

##### Create this type from $columnDefinition.

Returns null when column definition does not match.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinitoin` |   |  |





---

### ORM\Dbal\TypeInterface



#### Interface TypeInterface








#### Methods

* [fromDefinition](#ormdbaltypeinterfacefromdefinition) Create this type from $columnDefinition.

#### ORM\Dbal\TypeInterface::fromDefinition

```php?start_inline=true
public static function fromDefinition(
    $columnDefinition
): \ORM\Dbal\TypeInterface
```

##### Create this type from $columnDefinition.

Returns null when column definition does not match.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` |   |  |





---

### ORM\Exceptions\UndefinedRelation

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exceptions\UnsupportedDriver

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Type\VarChar

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### String data type

With and without max / fixed length




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$maxLength` | **integer** |  |



#### Methods

* [__construct](#ormdbaltypevarchar__construct) VarChar constructor.
* [factory](#ormdbaltypevarcharfactory) 
* [fromDefinition](#ormdbaltypevarcharfromdefinition) Create this type from $columnDefinition.

#### ORM\Dbal\Type\VarChar::__construct

```php?start_inline=true
public function __construct( integer $maxLength = null ): VarChar
```

##### VarChar constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$maxLength` | **integer**  |  |



#### ORM\Dbal\Type\VarChar::factory

```php?start_inline=true
public static function factory( $columnDefinition )
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` |   |  |



#### ORM\Dbal\Type\VarChar::fromDefinition

```php?start_inline=true
public static function fromDefinition(
    $columnDefinitoin
): \ORM\Dbal\TypeInterface
```

##### Create this type from $columnDefinition.

Returns null when column definition does not match.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\TypeInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinitoin` |   |  |





---

