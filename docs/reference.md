---
layout: default
title: API Reference
permalink: /reference.html
---
## API Reference


### ORM

* [BulkInsert](#ormbulkinsert)
* [DbConfig](#ormdbconfig)
* [Entity](#ormentity)
* [EntityFetcher](#ormentityfetcher)
* [EntityManager](#ormentitymanager)
* [Event](#ormevent)
* [Exception](#ormexception)
* [Namer](#ormnamer)
* [ObserverInterface](#ormobserverinterface)
* [Relation](#ormrelation)


### ORM\Dbal

* [Column](#ormdbalcolumn)
* [Dbal](#ormdbaldbal)
* [Error](#ormdbalerror)
* [Mysql](#ormdbalmysql)
* [Other](#ormdbalother)
* [Pgsql](#ormdbalpgsql)
* [Sqlite](#ormdbalsqlite)
* [Table](#ormdbaltable)
* [Type](#ormdbaltype)
* [TypeInterface](#ormdbaltypeinterface)


### ORM\Dbal\Error

* [InvalidJson](#ormdbalerrorinvalidjson)
* [NoBoolean](#ormdbalerrornoboolean)
* [NoDateTime](#ormdbalerrornodatetime)
* [NoNumber](#ormdbalerrornonumber)
* [NoString](#ormdbalerrornostring)
* [NotAllowed](#ormdbalerrornotallowed)
* [NoTime](#ormdbalerrornotime)
* [NotNullable](#ormdbalerrornotnullable)
* [NotValid](#ormdbalerrornotvalid)
* [TooLong](#ormdbalerrortoolong)


### ORM\Dbal\Type

* [Boolean](#ormdbaltypeboolean)
* [DateTime](#ormdbaltypedatetime)
* [Enum](#ormdbaltypeenum)
* [Json](#ormdbaltypejson)
* [Number](#ormdbaltypenumber)
* [Set](#ormdbaltypeset)
* [Text](#ormdbaltypetext)
* [Time](#ormdbaltypetime)
* [VarChar](#ormdbaltypevarchar)


### ORM\Entity

* [GeneratesPrimaryKeys](#ormentitygeneratesprimarykeys)


### ORM\Event

* [Deleted](#ormeventdeleted)
* [Deleting](#ormeventdeleting)
* [Fetched](#ormeventfetched)
* [Inserted](#ormeventinserted)
* [Inserting](#ormeventinserting)
* [Saved](#ormeventsaved)
* [Saving](#ormeventsaving)
* [Updated](#ormeventupdated)
* [UpdateEvent](#ormeventupdateevent)
* [Updating](#ormeventupdating)


### ORM\Exception

* [IncompletePrimaryKey](#ormexceptionincompleteprimarykey)
* [InvalidArgument](#ormexceptioninvalidargument)
* [InvalidConfiguration](#ormexceptioninvalidconfiguration)
* [InvalidName](#ormexceptioninvalidname)
* [InvalidRelation](#ormexceptioninvalidrelation)
* [NoConnection](#ormexceptionnoconnection)
* [NoEntity](#ormexceptionnoentity)
* [NoEntityManager](#ormexceptionnoentitymanager)
* [NoOperator](#ormexceptionnooperator)
* [NotJoined](#ormexceptionnotjoined)
* [NotScalar](#ormexceptionnotscalar)
* [UndefinedRelation](#ormexceptionundefinedrelation)
* [UnknownColumn](#ormexceptionunknowncolumn)
* [UnsupportedDriver](#ormexceptionunsupporteddriver)


### ORM\Observer

* [AbstractObserver](#ormobserverabstractobserver)
* [CallbackObserver](#ormobservercallbackobserver)


### ORM\QueryBuilder

* [Parenthesis](#ormquerybuilderparenthesis)
* [ParenthesisInterface](#ormquerybuilderparenthesisinterface)
* [QueryBuilder](#ormquerybuilderquerybuilder)
* [QueryBuilderInterface](#ormquerybuilderquerybuilderinterface)


### ORM\Relation

* [ManyToMany](#ormrelationmanytomany)
* [OneToMany](#ormrelationonetomany)
* [OneToOne](#ormrelationonetoone)
* [Owner](#ormrelationowner)


### ORM\Testing

* [EntityFetcherMock](#ormtestingentityfetchermock)
* [EntityManagerMock](#ormtestingentitymanagermock)


### ORM\Testing\EntityFetcherMock

* [Result](#ormtestingentityfetchermockresult)
* [ResultRepository](#ormtestingentityfetchermockresultrepository)


---

### ORM\Observer\AbstractObserver


**Implements:** [ORM\ObserverInterface](#ormobserverinterface)

#### AbstractObserver for entity events

When a handler returns false it will cancel other event handlers and if
applicable stops the execution (saving, inserting, updating and deleting
can be canceled).






#### Methods

* [deleted](#ormobserverabstractobserverdeleted) Gets called after an entity got deleted.
* [deleting](#ormobserverabstractobserverdeleting) Gets called before an entity gets deleted.
* [fetched](#ormobserverabstractobserverfetched) Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.
* [inserted](#ormobserverabstractobserverinserted) Gets called after an entity gets inserted.
* [inserting](#ormobserverabstractobserverinserting) Gets Called before an entity gets inserted.
* [saved](#ormobserverabstractobserversaved) Gets called after an entity got saved.
* [saving](#ormobserverabstractobserversaving) Gets called before an entity gets saved.
* [updated](#ormobserverabstractobserverupdated) Gets called after an entity got updated.
* [updating](#ormobserverabstractobserverupdating) Gets called before an entity gets updated.

#### ORM\Observer\AbstractObserver::deleted

```php
public function deleted( \ORM\Event\Deleted $event ): boolean
```

##### Gets called after an entity got deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Deleted**  |  |



#### ORM\Observer\AbstractObserver::deleting

```php
public function deleting( \ORM\Event\Deleting $event ): boolean
```

##### Gets called before an entity gets deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Deleting**  |  |



#### ORM\Observer\AbstractObserver::fetched

```php
public function fetched( \ORM\Event\Fetched $event ): boolean
```

##### Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Fetched**  |  |



#### ORM\Observer\AbstractObserver::inserted

```php
public function inserted( \ORM\Event\Inserted $event ): boolean
```

##### Gets called after an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Inserted**  |  |



#### ORM\Observer\AbstractObserver::inserting

```php
public function inserting( \ORM\Event\Inserting $event ): boolean
```

##### Gets Called before an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Inserting**  |  |



#### ORM\Observer\AbstractObserver::saved

```php
public function saved( \ORM\Event\Saved $event ): boolean
```

##### Gets called after an entity got saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Saved**  |  |



#### ORM\Observer\AbstractObserver::saving

```php
public function saving( \ORM\Event\Saving $event ): boolean
```

##### Gets called before an entity gets saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Saving**  |  |



#### ORM\Observer\AbstractObserver::updated

```php
public function updated( \ORM\Event\Updated $event ): boolean
```

##### Gets called after an entity got updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Updated**  |  |



#### ORM\Observer\AbstractObserver::updating

```php
public function updating( \ORM\Event\Updating $event ): boolean
```

##### Gets called before an entity gets updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Updating**  |  |





---

### ORM\Dbal\Type\Boolean

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Boolean data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$dbal` | ** \ ORM \ Dbal \ Dbal** |  |



#### Methods

* [__construct](#ormdbaltypeboolean__construct) Boolean constructor
* [factory](#ormdbaltypebooleanfactory) Returns a new Type object
* [fits](#ormdbaltypebooleanfits) Check if this type fits to $columnDefinition
* [getBoolean](#ormdbaltypebooleangetboolean) Get the string representation for boolean
* [validate](#ormdbaltypebooleanvalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Boolean::__construct

```php
public function __construct( \ORM\Dbal\Dbal $dbal ): Boolean
```

##### Boolean constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |



#### ORM\Dbal\Type\Boolean::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Boolean::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Boolean::getBoolean

```php
protected function getBoolean( boolean $bool ): string
```

##### Get the string representation for boolean



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$bool` | **boolean**  |  |



#### ORM\Dbal\Type\Boolean::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\BulkInsert









#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$class` | **string** |  |
| **protected** | `$dbal` | **Dbal \ Dbal** |  |
| **protected** | `$limit` | **integer** |  |
| **protected** | `$onSync` | **callable** |  |
| **protected** | `$useAutoIncrement` | **boolean** |  |
| **protected** | `$update` | **boolean** |  |
| **protected** | `$new` | **array&lt;Entity>** |  |
| **protected** | `$synced` | **array&lt;Entity>** |  |



#### Methods

* [__construct](#ormbulkinsert__construct) BulkInsert constructor.
* [add](#ormbulkinsertadd) Add an entity to the bulk insert.
* [execute](#ormbulkinsertexecute) Executes the bulk insert.
* [finish](#ormbulkinsertfinish) Insert the outstanding entities and return all synced objects.
* [getLimit](#ormbulkinsertgetlimit) 
* [limit](#ormbulkinsertlimit) Limit the amount of entities inserted at once.
* [noAutoIncrement](#ormbulkinsertnoautoincrement) Disable updating the primary key by auto increment.
* [noUpdates](#ormbulkinsertnoupdates) Disable updating entities after insert
* [onSync](#ormbulkinsertonsync) Executes $callback after insert
* [updateEntities](#ormbulkinsertupdateentities) Enable updating entities after insert
* [useAutoincrement](#ormbulkinsertuseautoincrement) Enable updating the primary keys from autoincrement

#### ORM\BulkInsert::__construct

```php
public function __construct(
    \ORM\Dbal\Dbal $dbal, string $class, integer $limit = 20
): BulkInsert
```

##### BulkInsert constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **Dbal\Dbal**  |  |
| `$class` | **string**  |  |
| `$limit` | **integer**  |  |



#### ORM\BulkInsert::add

```php
public function add( \ORM\Entity $entities )
```

##### Add an entity to the bulk insert.



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **Entity**  |  |



#### ORM\BulkInsert::execute

```php
protected function execute()
```

##### Executes the bulk insert.



**Visibility:** this method is **protected**.
<br />




#### ORM\BulkInsert::finish

```php
public function finish(): array<\ORM\Entity>
```

##### Insert the outstanding entities and return all synced objects.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />



#### ORM\BulkInsert::getLimit

```php
public function getLimit(): integer
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\BulkInsert::limit

```php
public function limit( integer $limit ): $this
```

##### Limit the amount of entities inserted at once.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  |  |



#### ORM\BulkInsert::noAutoIncrement

```php
public function noAutoIncrement(): $this
```

##### Disable updating the primary key by auto increment.

**Caution**: If this is disabled updating could cause a IncompletePrimaryKey exception.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### ORM\BulkInsert::noUpdates

```php
public function noUpdates(): $this
```

##### Disable updating entities after insert



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### ORM\BulkInsert::onSync

```php
public function onSync( callable $callback = null ): $this
```

##### Executes $callback after insert

Provides an array of the just inserted entities in first argument.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$callback` | **callable**  |  |



#### ORM\BulkInsert::updateEntities

```php
public function updateEntities(): $this
```

##### Enable updating entities after insert

**Caution**: This option will need to update the primary key by autoincrement which maybe is not supported
by your db access layer (DBAL).

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />



#### ORM\BulkInsert::useAutoincrement

```php
public function useAutoincrement(): $this
```

##### Enable updating the primary keys from autoincrement

**Caution**: Your db access layer (DBAL) may not support this feature.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />





---

### ORM\Observer\CallbackObserver

**Extends:** [ORM\Observer\AbstractObserver](#ormobserverabstractobserver)


#### AbstractObserver for entity events

When a handler returns false it will cancel other event handlers and if
applicable stops the execution (saving, inserting, updating and deleting
can be canceled).




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$callbacks` |  |  |



#### Methods

* [deleted](#ormobservercallbackobserverdeleted) Gets called after an entity got deleted.
* [deleting](#ormobservercallbackobserverdeleting) Gets called before an entity gets deleted.
* [executeCallbacks](#ormobservercallbackobserverexecutecallbacks) 
* [fetched](#ormobservercallbackobserverfetched) Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.
* [inserted](#ormobservercallbackobserverinserted) Gets called after an entity gets inserted.
* [inserting](#ormobservercallbackobserverinserting) Gets Called before an entity gets inserted.
* [off](#ormobservercallbackobserveroff) Remove all listeners for $event
* [on](#ormobservercallbackobserveron) Register a new $listener for $event
* [saved](#ormobservercallbackobserversaved) Gets called after an entity got saved.
* [saving](#ormobservercallbackobserversaving) Gets called before an entity gets saved.
* [updated](#ormobservercallbackobserverupdated) Gets called after an entity got updated.
* [updating](#ormobservercallbackobserverupdating) Gets called before an entity gets updated.

#### ORM\Observer\CallbackObserver::deleted

```php
public function deleted( \ORM\Event\Deleted $event ): boolean
```

##### Gets called after an entity got deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Deleted**  |  |



#### ORM\Observer\CallbackObserver::deleting

```php
public function deleting( \ORM\Event\Deleting $event ): boolean
```

##### Gets called before an entity gets deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Deleting**  |  |



#### ORM\Observer\CallbackObserver::executeCallbacks

```php
protected function executeCallbacks( \ORM\Event $event )
```




**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event**  |  |



#### ORM\Observer\CallbackObserver::fetched

```php
public function fetched( \ORM\Event\Fetched $event ): boolean
```

##### Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Fetched**  |  |



#### ORM\Observer\CallbackObserver::inserted

```php
public function inserted( \ORM\Event\Inserted $event ): boolean
```

##### Gets called after an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Inserted**  |  |



#### ORM\Observer\CallbackObserver::inserting

```php
public function inserting( \ORM\Event\Inserting $event ): boolean
```

##### Gets Called before an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Inserting**  |  |



#### ORM\Observer\CallbackObserver::off

```php
public function off( $event ): $this
```

##### Remove all listeners for $event



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` |   |  |



#### ORM\Observer\CallbackObserver::on

```php
public function on( $event, callable $listener ): $this
```

##### Register a new $listener for $event



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` |   |  |
| `$listener` | **callable**  |  |



#### ORM\Observer\CallbackObserver::saved

```php
public function saved( \ORM\Event\Saved $event ): boolean
```

##### Gets called after an entity got saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Saved**  |  |



#### ORM\Observer\CallbackObserver::saving

```php
public function saving( \ORM\Event\Saving $event ): boolean
```

##### Gets called before an entity gets saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Saving**  |  |



#### ORM\Observer\CallbackObserver::updated

```php
public function updated( \ORM\Event\Updated $event ): boolean
```

##### Gets called after an entity got updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Updated**  |  |



#### ORM\Observer\CallbackObserver::updating

```php
public function updating( \ORM\Event\Updating $event ): boolean
```

##### Gets called before an entity gets updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **\ORM\Event\Updating**  |  |





---

### ORM\Dbal\Column



#### Describes a column of a database table






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$registeredTypes` | **array&lt;string>** |  |
| **protected** | `$columnDefinition` | **array** |  |
| **protected** | `$dbal` | **Dbal** |  |
| **protected** | `$type` | **TypeInterface** |  |
| **protected** | `$hasDefault` | **boolean** |  |
| **protected** | `$isNullable` | **boolean** |  |



#### Methods

* [__construct](#ormdbalcolumn__construct) Column constructor.
* [__get](#ormdbalcolumn__get) Get attributes from column
* [getDefault](#ormdbalcolumngetdefault) Get the default value of the column
* [getName](#ormdbalcolumngetname) Get the name of the column
* [getNullable](#ormdbalcolumngetnullable) Get the nullable status of the column
* [getRegisteredType](#ormdbalcolumngetregisteredtype) Get the registered type for $columnDefinition
* [getType](#ormdbalcolumngettype) Determine and return the type
* [hasDefault](#ormdbalcolumnhasdefault) Check if default value is given
* [isNullable](#ormdbalcolumnisnullable) Check if the column is nullable
* [registerType](#ormdbalcolumnregistertype) Register $type for describe
* [validate](#ormdbalcolumnvalidate) Check if $value is valid for this type

#### ORM\Dbal\Column::__construct

```php
public function __construct(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): Column
```

##### Column constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Column::__get

```php
public function __get( string $name ): mixed
```

##### Get attributes from column



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |



#### ORM\Dbal\Column::getDefault

```php
public function getDefault(): mixed
```

##### Get the default value of the column



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />



#### ORM\Dbal\Column::getName

```php
public function getName(): string
```

##### Get the name of the column



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Column::getNullable

```php
public function getNullable(): boolean
```

##### Get the nullable status of the column



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Dbal\Column::getRegisteredType

```php
protected static function getRegisteredType( array $columnDefinition ): string
```

##### Get the registered type for $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Column::getType

```php
public function getType(): \ORM\Dbal\Type
```

##### Determine and return the type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Type**
<br />



#### ORM\Dbal\Column::hasDefault

```php
public function hasDefault(): boolean
```

##### Check if default value is given



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Dbal\Column::isNullable

```php
public function isNullable(): boolean
```

##### Check if the column is nullable



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Dbal\Column::registerType

```php
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



#### ORM\Dbal\Column::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Type\DateTime

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Date and datetime data type





#### Constants

| Name | Value |
|------|-------|
| DATE_REGEX | `'(\+|-)?\d{4,}-\d{2}-\d{2}'` |
| TIME_REGEX | `'\d{2}:\d{2}:\d{2}(\.\d{1,6})?'` |
| ZONE_REGEX | `'((\+|-)\d{1,2}(:?\d{2})?|Z)?'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$precision` | **integer** |  |
| **protected** | `$regex` | **string** |  |



#### Methods

* [__construct](#ormdbaltypedatetime__construct) DateTime constructor
* [factory](#ormdbaltypedatetimefactory) Returns a new Type object
* [fits](#ormdbaltypedatetimefits) Check if this type fits to $columnDefinition
* [getPrecision](#ormdbaltypedatetimegetprecision) 
* [validate](#ormdbaltypedatetimevalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\DateTime::__construct

```php
public function __construct(
    integer $precision = null, boolean $dateOnly = false
): DateTime
```

##### DateTime constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$precision` | **integer**  |  |
| `$dateOnly` | **boolean**  |  |



#### ORM\Dbal\Type\DateTime::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\DateTime::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\DateTime::getPrecision

```php
public function getPrecision(): integer
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\Dbal\Type\DateTime::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Dbal



#### Base class for database abstraction






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$quotingCharacter` | **string** |  |
| **protected** | `$identifierDivider` | **string** |  |
| **protected** | `$booleanTrue` | **string** |  |
| **protected** | `$booleanFalse` | **string** |  |
| **protected static** | `$typeMapping` | **array** |  |
| **protected static** | `$compositeWhereInTemplate` |  |  |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** |  |



#### Methods

* [__construct](#ormdbaldbal__construct) Dbal constructor.
* [assertSameType](#ormdbaldbalassertsametype) 
* [buildCompositeWhereInStatement](#ormdbaldbalbuildcompositewhereinstatement) Build a where in statement for composite primary keys
* [buildInsertStatement](#ormdbaldbalbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbaldbaldelete) Delete $entity from database
* [describe](#ormdbaldbaldescribe) Describe a table
* [escapeBoolean](#ormdbaldbalescapeboolean) Escape a boolean for query
* [escapeDateTime](#ormdbaldbalescapedatetime) Escape a date time object for query
* [escapeDouble](#ormdbaldbalescapedouble) Escape a double for Query
* [escapeIdentifier](#ormdbaldbalescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeInteger](#ormdbaldbalescapeinteger) Escape an integer for query
* [escapeNULL](#ormdbaldbalescapenull) Escape NULL for query
* [escapeString](#ormdbaldbalescapestring) Escape a string for query
* [escapeValue](#ormdbaldbalescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbaldbalextractparenthesis) Extract content from parenthesis in $type
* [insert](#ormdbaldbalinsert) Insert $entities into database
* [insertAndSync](#ormdbaldbalinsertandsync) Insert $entities and update with default values from database
* [insertAndSyncWithAutoInc](#ormdbaldbalinsertandsyncwithautoinc) Insert $entities and sync with auto increment primary key
* [normalizeType](#ormdbaldbalnormalizetype) Normalize $type
* [setOption](#ormdbaldbalsetoption) Set $option to $value
* [syncInserted](#ormdbaldbalsyncinserted) Sync the $entities after insert
* [updateAutoincrement](#ormdbaldbalupdateautoincrement) Update the autoincrement value

#### ORM\Dbal\Dbal::__construct

```php
public function __construct(
    \ORM\EntityManager $entityManager, array $options = array()
): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |
| `$options` | **array**  |  |



#### ORM\Dbal\Dbal::assertSameType

```php
protected static function assertSameType(
    array<\ORM\Entity> $entities
): boolean
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Dbal::buildCompositeWhereInStatement

```php
protected function buildCompositeWhereInStatement(
    array $cols, array $entities
): string
```

##### Build a where in statement for composite primary keys



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cols` | **array**  |  |
| `$entities` | **array**  |  |



#### ORM\Dbal\Dbal::buildInsertStatement

```php
protected function buildInsertStatement(
    \ORM\Entity $entity, array<\ORM\Entity> $entities
): string
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
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Dbal::delete

```php
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



#### ORM\Dbal\Dbal::describe

```php
public function describe(
    string $table
): \ORM\Dbal\Table|array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Table|array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />**Throws:** this method may throw **\ORM\Exception\UnsupportedDriver** or **\ORM\Exception**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\Dbal\Dbal::escapeBoolean

```php
protected function escapeBoolean( boolean $value ): string
```

##### Escape a boolean for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **boolean**  |  |



#### ORM\Dbal\Dbal::escapeDateTime

```php
protected function escapeDateTime( \DateTime $value ): mixed
```

##### Escape a date time object for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **\DateTime**  |  |



#### ORM\Dbal\Dbal::escapeDouble

```php
protected function escapeDouble( double $value ): string
```

##### Escape a double for Query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **double**  |  |



#### ORM\Dbal\Dbal::escapeIdentifier

```php
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



#### ORM\Dbal\Dbal::escapeInteger

```php
protected function escapeInteger( integer $value ): string
```

##### Escape an integer for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **integer**  |  |



#### ORM\Dbal\Dbal::escapeNULL

```php
protected function escapeNULL(): string
```

##### Escape NULL for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Dbal::escapeString

```php
protected function escapeString( string $value ): string
```

##### Escape a string for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **string**  |  |



#### ORM\Dbal\Dbal::escapeValue

```php
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Dbal::extractParenthesis

```php
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



#### ORM\Dbal\Dbal::insert

```php
public function insert( \ORM\Entity $entities ): boolean
```

##### Insert $entities into database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Dbal::insertAndSync

```php
public function insertAndSync( \ORM\Entity $entities ): boolean
```

##### Insert $entities and update with default values from database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Dbal::insertAndSyncWithAutoInc

```php
public function insertAndSyncWithAutoInc(
    \ORM\Entity $entities
): integer|boolean
```

##### Insert $entities and sync with auto increment primary key

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer|boolean**
<br />**Throws:** this method may throw **\ORM\Exception\UnsupportedDriver** or **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Dbal::normalizeType

```php
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



#### ORM\Dbal\Dbal::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Dbal\Dbal::syncInserted

```php
protected function syncInserted( \ORM\Entity $entities )
```

##### Sync the $entities after insert



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Dbal::updateAutoincrement

```php
protected function updateAutoincrement( \ORM\Entity $entity, integer $value )
```

##### Update the autoincrement value



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$value` | **integer &#124; string**  |  |





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

```php
public function __construct(
    string $type, string $name, string $user = null, string $pass = null, 
    string $host = null, string $port = null, array $attributes = array()
): DbConfig
```

##### Constructor

The constructor gets all parameters to establish a database connection and configure PDO instance.

Example:

```php
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

```php
public function getDsn(): string
```

##### Get the data source name



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />





---

### ORM\Event\Deleted

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'deleted'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Event\Deleting

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'deleting'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




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
| **protected** | `$data` | **array&lt;mixed>** | The current data of a row. |
| **protected static** | `$enableValidator` | **boolean** | Whether or not the validator for this class is enabled. |
| **protected static** | `$enabledValidators` | **array&lt;boolean>** | Whether or not the validator for a class got enabled during runtime. |
| **protected static** | `$relations` | **array** | Relation definitions |
| **protected** | `$entityManager` | **EntityManager** | The entity manager from which this entity got created |
| **protected** | `$relatedObjects` | **array** | Related objects for getRelated |
| **protected static** | `$tableNameTemplate` | **string** | The template to use to calculate the table name. |
| **protected static** | `$namingSchemeTable` | **string** | The naming scheme to use for table names. |
| **protected static** | `$namingSchemeColumn` | **string** | The naming scheme to use for column names. |
| **protected static** | `$namingSchemeMethods` | **string** | The naming scheme to use for method names. |
| **protected static** | `$tableName` | **string** | Fixed table name (ignore other settings) |
| **protected static** | `$namingSchemeAttributes` | **string** | The naming scheme to use for attributes. |
| **protected static** | `$columnAliases` | **array&lt;string>** | Fixed column names (ignore other settings) |
| **protected static** | `$columnPrefix` | **string** | A prefix for column names. |
| **protected static** | `$primaryKey` | **array&lt;string> &#124; string** | The variable(s) used for primary key. |
| **protected static** | `$autoIncrement` | **boolean** | Whether or not the primary key is auto incremented. |
| **protected static** | `$includedAttributes` | **array** | Additional attributes to show in toArray method |
| **protected static** | `$excludedAttributes` | **array** | Attributes to hide for toArray method (overruled by $attributes parameter) |
| **protected** | `$originalData` | **array&lt;mixed>** | The original data of the row. |



#### Methods

* [__construct](#ormentity__construct) Constructor
* [__get](#ormentity__get) 
* [__isset](#ormentity__isset) Check if a column is defined
* [__set](#ormentity__set) 
* [addRelated](#ormentityaddrelated) Add relations for $relation to $entities
* [deleteRelated](#ormentitydeleterelated) Delete relations for $relation to $entities
* [describe](#ormentitydescribe) Get a description for this table.
* [detachObserver](#ormentitydetachobserver) Stop observing the class by $observer
* [disableValidator](#ormentitydisablevalidator) Disable validator
* [enableValidator](#ormentityenablevalidator) Enable validator
* [fetch](#ormentityfetch) Fetches related objects
* [fill](#ormentityfill) Fill the entity with $data
* [generatePrimaryKey](#ormentitygenerateprimarykey) Generates a primary key
* [getAttribute](#ormentitygetattribute) Get the value from $attribute
* [getAttributeName](#ormentitygetattributename) Get the column name of $attribute
* [getColumnName](#ormentitygetcolumnname) Get the column name of $attribute
* [getDirty](#ormentitygetdirty) Get an array of attributes that changed
* [getNamingSchemeColumn](#ormentitygetnamingschemecolumn) 
* [getNamingSchemeMethods](#ormentitygetnamingschememethods) 
* [getNamingSchemeTable](#ormentitygetnamingschemetable) 
* [getPrimaryKey](#ormentitygetprimarykey) Get the primary key
* [getPrimaryKeyVars](#ormentitygetprimarykeyvars) Get the primary key vars
* [getRelated](#ormentitygetrelated) Get related objects
* [getRelation](#ormentitygetrelation) Get the definition for $relation
* [getTableName](#ormentitygettablename) Get the table name
* [getTableNameTemplate](#ormentitygettablenametemplate) 
* [hasPrimaryKey](#ormentityhasprimarykey) Check if the entity has has a complete primary key
* [insertEntity](#ormentityinsertentity) Insert the row in the database
* [isAutoIncremented](#ormentityisautoincremented) Check if the table has a auto increment column
* [isDirty](#ormentityisdirty) Checks if entity or $attribute got changed
* [isValid](#ormentityisvalid) Check if the current data is valid
* [isValidatorEnabled](#ormentityisvalidatorenabled) Check if the validator is enabled
* [observeBy](#ormentityobserveby) Observe the class using $observer
* [onChange](#ormentityonchange) Empty event handler
* [onInit](#ormentityoninit) Empty event handler
* [postPersist](#ormentitypostpersist) Empty event handler
* [postUpdate](#ormentitypostupdate) Empty event handler
* [prePersist](#ormentityprepersist) Empty event handler
* [preUpdate](#ormentitypreupdate) Empty event handler
* [query](#ormentityquery) Create an entityFetcher for this entity
* [reset](#ormentityreset) Resets the entity or $attribute to original data
* [resetRelated](#ormentityresetrelated) Resets all loaded relations or $relation
* [save](#ormentitysave) Save the entity to EntityManager
* [serialize](#ormentityserialize) String representation of data
* [setAttribute](#ormentitysetattribute) Set $attribute to $value
* [setEntityManager](#ormentitysetentitymanager) 
* [setNamingSchemeColumn](#ormentitysetnamingschemecolumn) 
* [setNamingSchemeMethods](#ormentitysetnamingschememethods) 
* [setNamingSchemeTable](#ormentitysetnamingschemetable) 
* [setRelated](#ormentitysetrelated) Set $relation to $entity
* [setTableNameTemplate](#ormentitysettablenametemplate) 
* [toArray](#ormentitytoarray) Get an array of the entity
* [unserialize](#ormentityunserialize) Constructs the object
* [updateEntity](#ormentityupdateentity) Update the row in the database
* [validate](#ormentityvalidate) Validate $value for $attribute
* [validateArray](#ormentityvalidatearray) Validate $data

#### ORM\Entity::__construct

```php
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

```php
public function __get( string $attribute ): mixed|null
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed|null**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  |  |



**See Also:**

* self::getAttribute 
#### ORM\Entity::__isset

```php
public function __isset( $attribute ): boolean
```

##### Check if a column is defined



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` |   |  |



#### ORM\Entity::__set

```php
public function __set( string $attribute, $value )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | The variable to change |
| `$value` | **mixed**  | The value to store |



**See Also:**

* self::getAttribute 
#### ORM\Entity::addRelated

```php
public function addRelated(
    string $relation, array<\ORM\Entity> $entities
)
```

##### Add relations for $relation to $entities

This method is only for many-to-many relations.

This method does not take care about already existing relations and will fail hard.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entities` | **array&lt;Entity>**  |  |



#### ORM\Entity::deleteRelated

```php
public function deleteRelated(
    string $relation, array<\ORM\Entity> $entities
)
```

##### Delete relations for $relation to $entities

This method is only for many-to-many relations.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entities` | **array&lt;Entity>**  |  |



#### ORM\Entity::describe

```php
public static function describe(): \ORM\Dbal\Table|array<\ORM\Dbal\Column>
```

##### Get a description for this table.



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Table|array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />



#### ORM\Entity::detachObserver

```php
public static function detachObserver(
    \ORM\Observer\AbstractObserver $observer
)
```

##### Stop observing the class by $observer



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$observer` | **Observer\AbstractObserver**  |  |



**See Also:**

* \ORM\EntityManager::detach() 
#### ORM\Entity::disableValidator

```php
public static function disableValidator( boolean $disable = true )
```

##### Disable validator



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$disable` | **boolean**  |  |



#### ORM\Entity::enableValidator

```php
public static function enableValidator( boolean $enable = true )
```

##### Enable validator



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$enable` | **boolean**  |  |



#### ORM\Entity::fetch

```php
public function fetch(
    string $relation, boolean $getAll = false
): \ORM\Entity|array<\ORM\Entity>|\ORM\EntityFetcher
```

##### Fetches related objects

For relations with cardinality many it returns an EntityFetcher. Otherwise it returns the entity.

It will throw an error for non owner when the key is incomplete.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity|array&lt;mixed,\ORM\Entity&gt;|\ORM\EntityFetcher**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  | The relation to fetch |
| `$getAll` | **boolean**  |  |



#### ORM\Entity::fill

```php
public function fill(
    array $data, boolean $ignoreUnknown = false, boolean $checkMissing = false
)
```

##### Fill the entity with $data

When $checkMissing is set to true it also proves that the absent columns are nullable.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\UnknownColumn** or **\ORM\Dbal\Error**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array**  |  |
| `$ignoreUnknown` | **boolean**  |  |
| `$checkMissing` | **boolean**  |  |



#### ORM\Entity::generatePrimaryKey

```php
protected function generatePrimaryKey()
```

##### Generates a primary key

This method should only be executed from save method.

**Visibility:** this method is **protected**.
<br />




#### ORM\Entity::getAttribute

```php
public function getAttribute( string $attribute ): mixed|null
```

##### Get the value from $attribute

If there is a custom getter this method get called instead.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed|null**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | The variable to get |



**See Also:**

* [Working with entities](https://tflori.github.io/orm/entities.html)

#### ORM\Entity::getAttributeName

```php
public static function getAttributeName( string $column ): string
```

##### Get the column name of $attribute

The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
to $namingSchemeColumn.

**ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
the same as getColumnName($name).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  |  |



#### ORM\Entity::getColumnName

```php
public static function getColumnName( string $attribute ): string
```

##### Get the column name of $attribute

The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
to $namingSchemeColumn.

**ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
the same as getColumnName($name).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  |  |



#### ORM\Entity::getDirty

```php
public function getDirty(): array
```

##### Get an array of attributes that changed

This method works on application level. Meaning it is showing additional attributes defined in
::$includedAttributes and and hiding ::$excludedAttributes.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Entity::getNamingSchemeColumn

```php
public static function getNamingSchemeColumn(): string
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getNamingSchemeMethods

```php
public static function getNamingSchemeMethods(): string
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getNamingSchemeTable

```php
public static function getNamingSchemeTable(): string
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getPrimaryKey

```php
public function getPrimaryKey(): array
```

##### Get the primary key



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />



#### ORM\Entity::getPrimaryKeyVars

```php
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



#### ORM\Entity::getRelated

```php
public function getRelated( string $relation, boolean $refresh = false ): mixed
```

##### Get related objects

The difference between getRelated and fetch is that getRelated stores the fetched entities. To refresh set
$refresh to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$refresh` | **boolean**  |  |



#### ORM\Entity::getRelation

```php
public static function getRelation( string $relation ): \ORM\Relation
```

##### Get the definition for $relation

It normalize the short definition form and create a Relation object from it.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />**Throws:** this method may throw **\ORM\Exception\UndefinedRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |



#### ORM\Entity::getTableName

```php
public static function getTableName(): string
```

##### Get the table name

The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
$tableName.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::getTableNameTemplate

```php
public static function getTableNameTemplate(): string
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Entity::hasPrimaryKey

```php
public function hasPrimaryKey(): boolean
```

##### Check if the entity has has a complete primary key



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Entity::insertEntity

```php
private function insertEntity(
    boolean $hasPrimaryKey
): \ORM\Event\Inserted|null
```

##### Insert the row in the database



**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\Event\Inserted|null**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$hasPrimaryKey` | **boolean**  |  |



#### ORM\Entity::isAutoIncremented

```php
public static function isAutoIncremented(): boolean
```

##### Check if the table has a auto increment column



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Entity::isDirty

```php
public function isDirty( string $attribute = null ): boolean
```

##### Checks if entity or $attribute got changed



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | Check only this variable or all variables |



#### ORM\Entity::isValid

```php
public function isValid(): boolean|array<\ORM\Dbal\Error>
```

##### Check if the current data is valid

Returns boolean true when valid otherwise an array of Errors.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|array&lt;mixed,\ORM\Dbal\Error&gt;**
<br />



#### ORM\Entity::isValidatorEnabled

```php
public static function isValidatorEnabled(): boolean
```

##### Check if the validator is enabled



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />



#### ORM\Entity::observeBy

```php
public static function observeBy(
    \ORM\?AbstractObserver $observer = null
): \ORM\?CallbackObserver
```

##### Observe the class using $observer

If AbstractObserver is omitted it returns a new CallbackObserver. Usage example:
```php
$em->observe(User::class)
    ->on('inserted', function (User $user) { ... })
    ->on('deleted', function (User $user) { ... });
```

For more information about model events please consult the [documentation](https://tflori.github.io/

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\?CallbackObserver**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$observer` | **?AbstractObserver**  |  |



**See Also:**

* \ORM\EntityManager::observe() 
#### ORM\Entity::onChange

```php
public function onChange( string $attribute, $oldValue, $value )
```

##### Empty event handler

Get called when something is changed with magic setter.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | The variable that got changed.merge(node.inheritedProperties) |
| `$oldValue` | **mixed**  | The old value of the variable |
| `$value` | **mixed**  | The new value of the variable |



#### ORM\Entity::onInit

```php
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

```php
public function postPersist()
```

##### Empty event handler

Get called after the entity got inserted in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::postUpdate

```php
public function postUpdate()
```

##### Empty event handler

Get called after the entity got updated in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::prePersist

```php
public function prePersist()
```

##### Empty event handler

Get called before the entity get inserted in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::preUpdate

```php
public function preUpdate()
```

##### Empty event handler

Get called before the entity get updated in database.

**Visibility:** this method is **public**.
<br />




#### ORM\Entity::query

```php
public static function query(): \ORM\EntityFetcher
```

##### Create an entityFetcher for this entity



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityFetcher**
<br />



#### ORM\Entity::reset

```php
public function reset( string $attribute = null )
```

##### Resets the entity or $attribute to original data



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | Reset only this variable or all variables |



#### ORM\Entity::resetRelated

```php
public function resetRelated( null $relation = null )
```

##### Resets all loaded relations or $relation

Helpful to reduce the size of serializations of the object (for caching, or toArray method etc.)

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **null**  |  |



#### ORM\Entity::save

```php
public function save(): \ORM\Entity
```

##### Save the entity to EntityManager



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />



#### ORM\Entity::serialize

```php
public function serialize(): string
```

##### String representation of data



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



**See Also:**

* [http://php.net/manual/en/serializable.serialize.php](http://php.net/manual/en/serializable.serialize.php)

#### ORM\Entity::setAttribute

```php
public function setAttribute( string $attribute, $value ): static
```

##### Set $attribute to $value

Tries to call custom setter before it stores the data directly. If there is a setter the setter needs to store
data that should be updated in the database to $data. Do not store data in $originalData as it will not be
written and give wrong results for dirty checking.

The onChange event is called after something got changed.

The method throws an error when the validation fails (also when the column does not exist).

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />**Throws:** this method may throw **\ORM\Dbal\Error**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | The variable to change |
| `$value` | **mixed**  | The value to store |



**See Also:**

* [Working with entities](https://tflori.github.io/orm/entities.html)

#### ORM\Entity::setEntityManager

```php
public function setEntityManager( \ORM\EntityManager $entityManager ): static
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  |  |



#### ORM\Entity::setNamingSchemeColumn

```php
public static function setNamingSchemeColumn( string $namingSchemeColumn )
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeColumn` | **string**  |  |



#### ORM\Entity::setNamingSchemeMethods

```php
public static function setNamingSchemeMethods( string $namingSchemeMethods )
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeMethods` | **string**  |  |



#### ORM\Entity::setNamingSchemeTable

```php
public static function setNamingSchemeTable( string $namingSchemeTable )
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$namingSchemeTable` | **string**  |  |



#### ORM\Entity::setRelated

```php
public function setRelated( string $relation, \ORM\Entity $entity = null )
```

##### Set $relation to $entity

This method is only for the owner of a relation.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$relation` | **string**  |  |
| `$entity` | **Entity**  |  |



#### ORM\Entity::setTableNameTemplate

```php
public static function setTableNameTemplate( string $tableNameTemplate )
```




**Warning:** this method is **deprecated**. This means that this method will likely be removed in a future version.
<br />**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableNameTemplate` | **string**  |  |



#### ORM\Entity::toArray

```php
public function toArray(
    array $attributes = array(), boolean $includeRelations = true
): array
```

##### Get an array of the entity



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attributes` | **array**  |  |
| `$includeRelations` | **boolean**  |  |



#### ORM\Entity::unserialize

```php
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

#### ORM\Entity::updateEntity

```php
private function updateEntity( array $dirty ): \ORM\Event\Updated|null
```

##### Update the row in the database



**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\Event\Updated|null**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dirty` | **array**  |  |



#### ORM\Entity::validate

```php
public static function validate(
    string $attribute, $value
): boolean|\ORM\Dbal\Error
```

##### Validate $value for $attribute



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Entity::validateArray

```php
public static function validateArray( array $data ): array
```

##### Validate $data

$data has to be an array of $attribute => $value

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$data` | **array**  |  |





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
* [count](#ormentityfetchercount) Get the count of the resulting items
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

```php
public function __construct(
    \ORM\EntityManager $entityManager, \ORM\Entity $class
): EntityFetcher
```

##### Constructor

Create a select statement for $tableName with an object oriented interface.

It uses static::$defaultEntityManager if $entityManager is not given.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **EntityManager**  | EntityManager where to store the fetched entities |
| `$class` | **Entity &#124; string**  | Class to fetch |



#### ORM\EntityFetcher::all

```php
public function all( integer $limit ): array<\ORM\Entity>
```

##### Fetch an array of entities

When no $limit is set it fetches all entities in result set.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | Maximum number of entities to fetch |



#### ORM\EntityFetcher::andParenthesis

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\EntityFetcher::andWhere

```php
public function andWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\EntityFetcher::buildExpression

```php
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

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\EntityFetcher::column

```php
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

```php
public function columns( array $columns = null ): static
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\EntityFetcher::convertPlaceholders

```php
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
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Argument(s) to insert |
| `$translateCols` | **boolean**  | Whether or not column names should be translated |



#### ORM\EntityFetcher::count

```php
public function count(): integer
```

##### Get the count of the resulting items



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\EntityFetcher::createRelatedJoin

```php
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

```php
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::getDefaultOperator

```php
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\EntityFetcher::getEntityManager

```php
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



#### ORM\EntityFetcher::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\EntityFetcher::getQuery

```php
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\EntityFetcher::getStatement

```php
private function getStatement(): \PDOStatement|boolean
```

##### Query database and return result

Queries the database with current query and returns the resulted PDOStatement.

If query failed it returns false. It also stores this failed result and to change the query afterwards will not
change the result.

**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\PDOStatement|boolean**
<br />



#### ORM\EntityFetcher::groupBy

```php
public function groupBy( string $column, array $args = array() ): static
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::join

```php
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::joinRelated

```php
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

```php
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::leftJoinRelated

```php
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

```php
public function limit( integer $limit ): static
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\EntityFetcher::modifier

```php
public function modifier( string $modifier ): static
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\EntityFetcher::offset

```php
public function offset( integer $offset ): static
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\EntityFetcher::one

```php
public function one(): \ORM\Entity
```

##### Fetch one entity

If there is no more entity in the result set it returns null.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />



#### ORM\EntityFetcher::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): static
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\EntityFetcher::orWhere

```php
public function orWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\EntityFetcher::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\EntityFetcher::rightJoin

```php
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\EntityFetcher::setQuery

```php
public function setQuery( string $query, array $args = null ): $this
```

##### Set a raw query or use different QueryBuilder

For easier use and against sql injection it allows question mark placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string &#124; QueryBuilder\QueryBuilderInterface**  | Raw query string or a QueryBuilderInterface |
| `$args` | **array**  | The arguments for placeholders |



#### ORM\EntityFetcher::where

```php
public function where(
    string $column, string $operator = null, string $value = null
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |





---

### ORM\Testing\EntityFetcherMock

**Extends:** [ORM\EntityFetcher](#ormentityfetcher)


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
| **protected** | `$class` | **string &#124;  \ ORM \ Entity** | The entity class that we want to fetch |
| **protected** | `$result` | ** \ PDOStatement** | The result object from PDO |
| **protected** | `$query` | **string &#124;  \ ORM \ QueryBuilder \ QueryBuilderInterface** | The query to execute (overwrites other settings) |
| **protected** | `$classMapping` | **array&lt;string[]>** | The class to alias mapping and vise versa |
| **protected** | `$tableName` | **string** | The table to query |
| **protected** | `$alias` | **string** | The alias of the main table |
| **protected** | `$columns` | **array &#124; null** | Columns to fetch (null is equal to [&#039;*&#039;]) |
| **protected** | `$joins` | **array&lt;string>** | Joins get concatenated with space |
| **protected** | `$limit` | **integer** | Limit amount of rows |
| **protected** | `$offset` | **integer** | Offset to start from |
| **protected** | `$groupBy` | **array&lt;string>** | Group by conditions get concatenated with comma |
| **protected** | `$orderBy` | **array&lt;string>** | Order by conditions get concatenated with comma |
| **protected** | `$modifier` | **array&lt;string>** | Modifiers get concatenated with space |
| **public** | `$entityManager` | **EntityManagerMock** |  |
| **public static** | `$defaultEntityManager` | ** \ ORM \ EntityManager** | The default EntityManager to use to for quoting |
| **protected** | `$where` | **array&lt;string>** | Where conditions get concatenated with space |
| **protected** | `$onClose` | **callable** | Callback to close the parenthesis |
| **protected** | `$parent` | ** \ ORM \ QueryBuilder \ ParenthesisInterface** | Parent parenthesis or query |
| **protected** | `$currentResult` | **array** |  |



#### Methods

* [__construct](#ormtestingentityfetchermock__construct) Constructor
* [all](#ormtestingentityfetchermockall) Fetch an array of entities
* [andParenthesis](#ormtestingentityfetchermockandparenthesis) Add a parenthesis with AND
* [andWhere](#ormtestingentityfetchermockandwhere) Add a where condition with AND.
* [buildExpression](#ormtestingentityfetchermockbuildexpression) 
* [close](#ormtestingentityfetchermockclose) Close parenthesis
* [column](#ormtestingentityfetchermockcolumn) Add $column
* [columns](#ormtestingentityfetchermockcolumns) Set $columns
* [convertPlaceholders](#ormtestingentityfetchermockconvertplaceholders) Replaces question marks in $expression with $args
* [count](#ormtestingentityfetchermockcount) Get the count of the resulting items
* [createRelatedJoin](#ormtestingentityfetchermockcreaterelatedjoin) Create the join with $join type
* [fullJoin](#ormtestingentityfetchermockfulljoin) Full (outer) join $tableName with $options
* [getDefaultOperator](#ormtestingentityfetchermockgetdefaultoperator) 
* [getEntityManager](#ormtestingentityfetchermockgetentitymanager) 
* [getExpression](#ormtestingentityfetchermockgetexpression) Get the expression
* [getQuery](#ormtestingentityfetchermockgetquery) Get the query / select statement
* [getStatement](#ormtestingentityfetchermockgetstatement) Query database and return result
* [groupBy](#ormtestingentityfetchermockgroupby) Group By $column
* [join](#ormtestingentityfetchermockjoin) (Inner) join $tableName with $options
* [joinRelated](#ormtestingentityfetchermockjoinrelated) Join $relation
* [leftJoin](#ormtestingentityfetchermockleftjoin) Left (outer) join $tableName with $options
* [leftJoinRelated](#ormtestingentityfetchermockleftjoinrelated) Left outer join $relation
* [limit](#ormtestingentityfetchermocklimit) Set $limit
* [modifier](#ormtestingentityfetchermockmodifier) Add $modifier
* [offset](#ormtestingentityfetchermockoffset) Set $offset
* [one](#ormtestingentityfetchermockone) Fetch one entity
* [orderBy](#ormtestingentityfetchermockorderby) Order By $column in $direction
* [orParenthesis](#ormtestingentityfetchermockorparenthesis) Add a parenthesis with OR
* [orWhere](#ormtestingentityfetchermockorwhere) Add a where condition with OR.
* [parenthesis](#ormtestingentityfetchermockparenthesis) Alias for andParenthesis
* [rightJoin](#ormtestingentityfetchermockrightjoin) Right (outer) join $tableName with $options
* [setQuery](#ormtestingentityfetchermocksetquery) Set a raw query or use different QueryBuilder
* [where](#ormtestingentityfetchermockwhere) Alias for andWhere

#### ORM\Testing\EntityFetcherMock::__construct

```php
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
| `$parent` | **\ORM\QueryBuilder\ParenthesisInterface**  | Parent where createWhereCondition get executed |



#### ORM\Testing\EntityFetcherMock::all

```php
public function all( integer $limit ): array<\ORM\Entity>
```

##### Fetch an array of entities

When no $limit is set it fetches all entities in result set.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | Maximum number of entities to fetch |



#### ORM\Testing\EntityFetcherMock::andParenthesis

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock::andWhere

```php
public function andWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\Testing\EntityFetcherMock::buildExpression

```php
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



#### ORM\Testing\EntityFetcherMock::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\Testing\EntityFetcherMock::column

```php
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



#### ORM\Testing\EntityFetcherMock::columns

```php
public function columns( array $columns = null ): static
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\Testing\EntityFetcherMock::convertPlaceholders

```php
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces question marks in $expression with $args



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Arguments for placeholders |



#### ORM\Testing\EntityFetcherMock::count

```php
public function count(): integer
```

##### Get the count of the resulting items



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\Testing\EntityFetcherMock::createRelatedJoin

```php
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



#### ORM\Testing\EntityFetcherMock::fullJoin

```php
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::getDefaultOperator

```php
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\Testing\EntityFetcherMock::getEntityManager

```php
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



#### ORM\Testing\EntityFetcherMock::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Testing\EntityFetcherMock::getQuery

```php
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Testing\EntityFetcherMock::getStatement

```php
private function getStatement(): \PDOStatement|boolean
```

##### Query database and return result

Queries the database with current query and returns the resulted PDOStatement.

If query failed it returns false. It also stores this failed result and to change the query afterwards will not
change the result.

**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\PDOStatement|boolean**
<br />



#### ORM\Testing\EntityFetcherMock::groupBy

```php
public function groupBy( string $column, array $args = array() ): static
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::join

```php
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::joinRelated

```php
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



#### ORM\Testing\EntityFetcherMock::leftJoin

```php
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::leftJoinRelated

```php
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



#### ORM\Testing\EntityFetcherMock::limit

```php
public function limit( integer $limit ): static
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\Testing\EntityFetcherMock::modifier

```php
public function modifier( string $modifier ): static
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\Testing\EntityFetcherMock::offset

```php
public function offset( integer $offset ): static
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\Testing\EntityFetcherMock::one

```php
public function one(): \ORM\Entity
```

##### Fetch one entity

If there is no more entity in the result set it returns null.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />



#### ORM\Testing\EntityFetcherMock::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): static
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock::orWhere

```php
public function orWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\Testing\EntityFetcherMock::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock::rightJoin

```php
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock::setQuery

```php
public function setQuery( string $query, array $args = null ): $this
```

##### Set a raw query or use different QueryBuilder

For easier use and against sql injection it allows question mark placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string &#124; \ORM\QueryBuilder\QueryBuilderInterface**  | Raw query string or a QueryBuilderInterface |
| `$args` | **array**  | The arguments for placeholders |



#### ORM\Testing\EntityFetcherMock::where

```php
public function where(
    string $column, string $operator = null, string $value = null
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
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
| OPT_NAMING_SCHEME_ATTRIBUTE | `'namingSchemeAttribute'` |
| OPT_QUOTING_CHARACTER | `'quotingChar'` |
| OPT_IDENTIFIER_DIVIDER | `'identifierDivider'` |
| OPT_BOOLEAN_TRUE | `'true'` |
| OPT_BOOLEAN_FALSE | `'false'` |
| OPT_DBAL_CLASS | `'dbalClass'` |
| OPT_MYSQL_BOOLEAN_TRUE | `'mysqlTrue'` |
| OPT_MYSQL_BOOLEAN_FALSE | `'mysqlFalse'` |
| OPT_SQLITE_BOOLEAN_TRUE | `'sqliteTrue'` |
| OPT_SQLITE_BOOLEAN_FALSE | `'sqliteFalse'` |
| OPT_PGSQL_BOOLEAN_TRUE | `'pgsqlTrue'` |
| OPT_PGSQL_BOOLEAN_FALSE | `'pgsqlFalse'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$resolver` | **callable** |  |
| **protected** | `$connection` | ** \ PDO &#124; callable &#124; DbConfig** | Connection to database |
| **protected** | `$dbal` | **Dbal \ Dbal** | The Database Abstraction Layer |
| **protected** | `$namer` | **Namer** | The Namer instance |
| **protected** | `$map` | **array&lt;Entity[]>** | The Entity map |
| **protected** | `$options` | **array** | The options set for this instance |
| **protected** | `$descriptions` | **array&lt;Dbal \ Table> &#124; array&lt;Dbal \ Column[]>** | Already fetched column descriptions |
| **protected** | `$bulkInserts` | **array&lt;BulkInsert>** | Classes forcing bulk insert |
| **protected static** | `$emMapping` | **EntityManager[string] &#124; EntityManager[string][string]** | Mapping for EntityManager instances |
| **protected** | `$observers` | **array&lt;Observer \ AbstractObserver[]>** |  |



#### Methods

* [__construct](#ormentitymanager__construct) Constructor
* [buildChecksum](#ormentitymanagerbuildchecksum) Build a checksum from $primaryKey
* [buildPrimaryKey](#ormentitymanagerbuildprimarykey) Builds the primary key with column names as keys
* [defineForNamespace](#ormentitymanagerdefinefornamespace) Define $this EntityManager as the default EntityManager for $nameSpace
* [defineForParent](#ormentitymanagerdefineforparent) Define $this EntityManager as the default EntityManager for subClasses of $class
* [delete](#ormentitymanagerdelete) Delete $entity from database
* [describe](#ormentitymanagerdescribe) Returns an array of columns from $table.
* [detach](#ormentitymanagerdetach) Detach $observer from all classes
* [escapeIdentifier](#ormentitymanagerescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormentitymanagerescapevalue) Returns $value formatted to use in a sql statement.
* [fetch](#ormentitymanagerfetch) Fetch one or more entities
* [finishBulkInserts](#ormentitymanagerfinishbulkinserts) Finish the bulk insert for $class.
* [getConnection](#ormentitymanagergetconnection) Get the pdo connection.
* [getDbal](#ormentitymanagergetdbal) Get the Datbase Abstraction Layer
* [getInstance](#ormentitymanagergetinstance) Get an instance of the EntityManager.
* [getInstanceByNameSpace](#ormentitymanagergetinstancebynamespace) Get the instance by NameSpace mapping
* [getInstanceByParent](#ormentitymanagergetinstancebyparent) Get the instance by Parent class mapping
* [getNamer](#ormentitymanagergetnamer) Get the Namer instance
* [getOption](#ormentitymanagergetoption) Get $option
* [map](#ormentitymanagermap) Map $entity in the entity map
* [observe](#ormentitymanagerobserve) Observe $class using $observer
* [setConnection](#ormentitymanagersetconnection) Add connection after instantiation
* [setOption](#ormentitymanagersetoption) Set $option to $value
* [setResolver](#ormentitymanagersetresolver) Overwrite the functionality of ::getInstance($class) by $resolver($class)
* [sync](#ormentitymanagersync) Synchronizing $entity with database
* [useBulkInserts](#ormentitymanagerusebulkinserts) Force $class to use bulk insert.

#### ORM\EntityManager::__construct

```php
public function __construct( array $options = array() ): EntityManager
```

##### Constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` | **array**  | Options for the new EntityManager |



#### ORM\EntityManager::buildChecksum

```php
protected static function buildChecksum( array $primaryKey ): string
```

##### Build a checksum from $primaryKey



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$primaryKey` | **array**  |  |



#### ORM\EntityManager::buildPrimaryKey

```php
protected static function buildPrimaryKey(
    string $class, array $primaryKey
): array
```

##### Builds the primary key with column names as keys



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string &#124; Entity**  |  |
| `$primaryKey` | **array**  |  |



#### ORM\EntityManager::defineForNamespace

```php
public function defineForNamespace( $nameSpace ): static
```

##### Define $this EntityManager as the default EntityManager for $nameSpace



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$nameSpace` |   |  |



#### ORM\EntityManager::defineForParent

```php
public function defineForParent( $class ): static
```

##### Define $this EntityManager as the default EntityManager for subClasses of $class



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\EntityManager::delete

```php
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

```php
public function describe(
    string $table
): array<\ORM\Dbal\Column>|\ORM\Dbal\Table
```

##### Returns an array of columns from $table.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;|\ORM\Dbal\Table**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\EntityManager::detach

```php
public function detach(
    \ORM\ObserverInterface $observer, \ORM\?string $from = null
): boolean
```

##### Detach $observer from all classes

If the observer is attached to multiple classes all are removed except the optional parameter
$from defines from which class to remove the $observer.

Returns whether or not an observer got detached.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$observer` | **ObserverInterface**  |  |
| `$from` | **?string**  |  |



#### ORM\EntityManager::escapeIdentifier

```php
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

```php
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

```php
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
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey** or **\ORM\Exception\NoEntity**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  | The entity class you want to fetch |
| `$primaryKey` | **mixed**  | The primary key of the entity you want to fetch |



#### ORM\EntityManager::finishBulkInserts

```php
public function finishBulkInserts( $class ): array<\ORM\Entity>
```

##### Finish the bulk insert for $class.

Returns an array of entities added.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\EntityManager::getConnection

```php
public function getConnection(): \PDO
```

##### Get the pdo connection.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\PDO**
<br />**Throws:** this method may throw **\ORM\Exception\NoConnection** or **\ORM\Exception\NoConnection**<br />



#### ORM\EntityManager::getDbal

```php
public function getDbal(): \ORM\Dbal\Dbal
```

##### Get the Datbase Abstraction Layer



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Dbal**
<br />



#### ORM\EntityManager::getInstance

```php
public static function getInstance( string $class = null ): \ORM\EntityManager
```

##### Get an instance of the EntityManager.

If no class is given it gets $class from backtrace.

It first tries to get the EntityManager for the Namespace of $class, then for the parents of $class. If no
EntityManager is found it returns the last created EntityManager (null if no EntityManager got created).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |



#### ORM\EntityManager::getInstanceByNameSpace

```php
private static function getInstanceByNameSpace( $class ): \ORM\EntityManager
```

##### Get the instance by NameSpace mapping



**Static:** this method is **static**.
<br />**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\EntityManager::getInstanceByParent

```php
private static function getInstanceByParent( $class ): \ORM\EntityManager
```

##### Get the instance by Parent class mapping



**Static:** this method is **static**.
<br />**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\EntityManager::getNamer

```php
public function getNamer(): \ORM\Namer
```

##### Get the Namer instance



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Namer**
<br />



#### ORM\EntityManager::getOption

```php
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

```php
public function map(
    \ORM\Entity $entity, boolean $update = false, string $class = null
): \ORM\Entity
```

##### Map $entity in the entity map

Returns the given entity or an entity that previously got mapped. This is useful to work in every function with
the same object.

```php
$user = $enitityManager->map(new User(['id' => 42]));
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |
| `$update` | **boolean**  | Update the entity map |
| `$class` | **string**  | Overwrite the class |



#### ORM\EntityManager::observe

```php
public function observe(
    string $class, \ORM\?ObserverInterface $observer = null
): \ORM\?CallbackObserver
```

##### Observe $class using $observer

If AbstractObserver is omitted it returns a new CallbackObserver. Usage example:
```php
$em->observe(User::class)
    ->on('inserted', function (User $user) { ... })
    ->on('deleted', function (User $user) { ... });
```

For more information about model events please consult the [documentation](https://tflori.github.io/

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\?CallbackObserver**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$observer` | **?ObserverInterface**  |  |



#### ORM\EntityManager::setConnection

```php
public function setConnection( $connection )
```

##### Add connection after instantiation

The connection can be an array of parameters for DbConfig::__construct(), a callable function that returns a PDO
instance, an instance of DbConfig or a PDO instance itself.

When it is not a PDO instance the connection get established on first use.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$connection` | **mixed**  | A configuration for (or a) PDO instance |



#### ORM\EntityManager::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  | One of OPT_* constants |
| `$value` | **mixed**  |  |



#### ORM\EntityManager::setResolver

```php
public static function setResolver( callable $resolver )
```

##### Overwrite the functionality of ::getInstance($class) by $resolver($class)



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resolver` | **callable**  |  |



#### ORM\EntityManager::sync

```php
public function sync( \ORM\Entity $entity, boolean $reset = false ): boolean
```

##### Synchronizing $entity with database

If $reset is true it also calls reset() on $entity.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |
| `$reset` | **boolean**  | Reset entities current data |



#### ORM\EntityManager::useBulkInserts

```php
public function useBulkInserts(
    string $class, integer $limit = 20
): \ORM\BulkInsert
```

##### Force $class to use bulk insert.

At the end you should call finish bulk insert otherwise you may loose data.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\BulkInsert**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$limit` | **integer**  | Maximum number of rows per insert |





---

### ORM\Testing\EntityManagerMock

**Extends:** [ORM\EntityManager](#ormentitymanager)


#### The EntityManager that manages the instances of Entities.






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$resolver` | **callable** |  |
| **protected** | `$connection` | ** \ PDO &#124; callable &#124;  \ ORM \ DbConfig** | Connection to database |
| **protected** | `$dbal` | ** \ ORM \ Dbal \ Dbal** | The Database Abstraction Layer |
| **protected** | `$namer` | ** \ ORM \ Namer** | The Namer instance |
| **protected** | `$map` | **array&lt; \ ORM \ Entity[]>** | The Entity map |
| **protected** | `$options` | **array** | The options set for this instance |
| **protected** | `$descriptions` | **array&lt; \ ORM \ Dbal \ Table> &#124; array&lt; \ ORM \ Dbal \ Column[]>** | Already fetched column descriptions |
| **protected** | `$bulkInserts` | **array&lt; \ ORM \ BulkInsert>** | Classes forcing bulk insert |
| **protected static** | `$emMapping` | ** \ ORM \ EntityManager[string] &#124;  \ ORM \ EntityManager[string][string]** | Mapping for EntityManager instances |
| **protected** | `$observers` | **array&lt; \ ORM \ Observer \ AbstractObserver[]>** |  |
| **protected** | `$resultRepository` |  |  |



#### Methods

* [__construct](#ormtestingentitymanagermock__construct) Constructor
* [addEntity](#ormtestingentitymanagermockaddentity) Add an entity to be fetched by primary key
* [addResult](#ormtestingentitymanagermockaddresult) Create and add a EntityFetcherMock\Result for $class
* [buildChecksum](#ormtestingentitymanagermockbuildchecksum) Build a checksum from $primaryKey
* [buildPrimaryKey](#ormtestingentitymanagermockbuildprimarykey) Builds the primary key with column names as keys
* [defineForNamespace](#ormtestingentitymanagermockdefinefornamespace) Define $this EntityManager as the default EntityManager for $nameSpace
* [defineForParent](#ormtestingentitymanagermockdefineforparent) Define $this EntityManager as the default EntityManager for subClasses of $class
* [delete](#ormtestingentitymanagermockdelete) Delete $entity from database
* [describe](#ormtestingentitymanagermockdescribe) Returns an array of columns from $table.
* [detach](#ormtestingentitymanagermockdetach) Detach $observer from all classes
* [escapeIdentifier](#ormtestingentitymanagermockescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeValue](#ormtestingentitymanagermockescapevalue) Returns $value formatted to use in a sql statement.
* [fetch](#ormtestingentitymanagermockfetch) Fetch one or more entities
* [finishBulkInserts](#ormtestingentitymanagermockfinishbulkinserts) Finish the bulk insert for $class.
* [getConnection](#ormtestingentitymanagermockgetconnection) Get the pdo connection.
* [getDbal](#ormtestingentitymanagermockgetdbal) Get the Datbase Abstraction Layer
* [getInstance](#ormtestingentitymanagermockgetinstance) Get an instance of the EntityManager.
* [getInstanceByNameSpace](#ormtestingentitymanagermockgetinstancebynamespace) Get the instance by NameSpace mapping
* [getInstanceByParent](#ormtestingentitymanagermockgetinstancebyparent) Get the instance by Parent class mapping
* [getNamer](#ormtestingentitymanagermockgetnamer) Get the Namer instance
* [getOption](#ormtestingentitymanagermockgetoption) Get $option
* [getResults](#ormtestingentitymanagermockgetresults) Get the results for $class and $query
* [map](#ormtestingentitymanagermockmap) Map $entity in the entity map
* [observe](#ormtestingentitymanagermockobserve) Observe $class using $observer
* [retrieve](#ormtestingentitymanagermockretrieve) Retrieve an entity by $primaryKey
* [setConnection](#ormtestingentitymanagermocksetconnection) Add connection after instantiation
* [setOption](#ormtestingentitymanagermocksetoption) Set $option to $value
* [setResolver](#ormtestingentitymanagermocksetresolver) Overwrite the functionality of ::getInstance($class) by $resolver($class)
* [sync](#ormtestingentitymanagermocksync) Synchronizing $entity with database
* [useBulkInserts](#ormtestingentitymanagermockusebulkinserts) Force $class to use bulk insert.

#### ORM\Testing\EntityManagerMock::__construct

```php
public function __construct( array $options = array() ): EntityManagerMock
```

##### Constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` | **array**  | Options for the new EntityManager |



#### ORM\Testing\EntityManagerMock::addEntity

```php
public function addEntity( \ORM\Entity $entity )
```

##### Add an entity to be fetched by primary key

The entity needs to have a primary key if not it will be filled with random values between RANDOM_KEY_MIN and
RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).

You can pass mocks from Entity too but we need to call `Entity::getPrimaryKey()`.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Testing\EntityManagerMock::addResult

```php
public function addResult(
    $class, \ORM\Entity $entities
): \ORM\Testing\EntityFetcherMock\Result|\Mockery\MockInterface
```

##### Create and add a EntityFetcherMock\Result for $class

As the results are mocked to come from the database they will also get a primary key if they don't have already.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Testing\EntityFetcherMock\Result|\Mockery\MockInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Testing\EntityManagerMock::buildChecksum

```php
protected static function buildChecksum( array $primaryKey ): string
```

##### Build a checksum from $primaryKey



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$primaryKey` | **array**  |  |



#### ORM\Testing\EntityManagerMock::buildPrimaryKey

```php
protected static function buildPrimaryKey(
    string $class, array $primaryKey
): array
```

##### Builds the primary key with column names as keys



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string &#124; \ORM\Entity**  |  |
| `$primaryKey` | **array**  |  |



#### ORM\Testing\EntityManagerMock::defineForNamespace

```php
public function defineForNamespace( $nameSpace ): static
```

##### Define $this EntityManager as the default EntityManager for $nameSpace



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$nameSpace` |   |  |



#### ORM\Testing\EntityManagerMock::defineForParent

```php
public function defineForParent( $class ): static
```

##### Define $this EntityManager as the default EntityManager for subClasses of $class



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\Testing\EntityManagerMock::delete

```php
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



#### ORM\Testing\EntityManagerMock::describe

```php
public function describe(
    string $table
): array<\ORM\Dbal\Column>|\ORM\Dbal\Table
```

##### Returns an array of columns from $table.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Dbal\Column&gt;|\ORM\Dbal\Table**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\Testing\EntityManagerMock::detach

```php
public function detach(
    \ORM\ObserverInterface $observer, \ORM\?string $from = null
): boolean
```

##### Detach $observer from all classes

If the observer is attached to multiple classes all are removed except the optional parameter
$from defines from which class to remove the $observer.

Returns whether or not an observer got detached.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$observer` | **\ORM\ObserverInterface**  |  |
| `$from` | **\ORM\?string**  |  |



#### ORM\Testing\EntityManagerMock::escapeIdentifier

```php
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



#### ORM\Testing\EntityManagerMock::escapeValue

```php
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



#### ORM\Testing\EntityManagerMock::fetch

```php
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
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  | The entity class you want to fetch |
| `$primaryKey` | **mixed**  | The primary key of the entity you want to fetch |



#### ORM\Testing\EntityManagerMock::finishBulkInserts

```php
public function finishBulkInserts( $class ): array<\ORM\Entity>
```

##### Finish the bulk insert for $class.

Returns an array of entities added.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\Testing\EntityManagerMock::getConnection

```php
public function getConnection(): \PDO
```

##### Get the pdo connection.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\PDO**
<br />**Throws:** this method may throw **\ORM\Exception\NoConnection** or **\ORM\Exception\NoConnection**<br />



#### ORM\Testing\EntityManagerMock::getDbal

```php
public function getDbal(): \ORM\Dbal\Dbal
```

##### Get the Datbase Abstraction Layer



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Dbal**
<br />



#### ORM\Testing\EntityManagerMock::getInstance

```php
public static function getInstance( string $class = null ): \ORM\EntityManager
```

##### Get an instance of the EntityManager.

If no class is given it gets $class from backtrace.

It first tries to get the EntityManager for the Namespace of $class, then for the parents of $class. If no
EntityManager is found it returns the last created EntityManager (null if no EntityManager got created).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |



#### ORM\Testing\EntityManagerMock::getInstanceByNameSpace

```php
private static function getInstanceByNameSpace( $class ): \ORM\EntityManager
```

##### Get the instance by NameSpace mapping



**Static:** this method is **static**.
<br />**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\Testing\EntityManagerMock::getInstanceByParent

```php
private static function getInstanceByParent( $class ): \ORM\EntityManager
```

##### Get the instance by Parent class mapping



**Static:** this method is **static**.
<br />**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |



#### ORM\Testing\EntityManagerMock::getNamer

```php
public function getNamer(): \ORM\Namer
```

##### Get the Namer instance



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Namer**
<br />



#### ORM\Testing\EntityManagerMock::getOption

```php
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



#### ORM\Testing\EntityManagerMock::getResults

```php
public function getResults( string $class, \ORM\EntityFetcher $fetcher ): array
```

##### Get the results for $class and $query

The EntityFetcherMock\Result gets a quality for matching this query. Only the highest quality will be used.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$fetcher` | **\ORM\EntityFetcher**  |  |



#### ORM\Testing\EntityManagerMock::map

```php
public function map(
    \ORM\Entity $entity, boolean $update = false, string $class = null
): \ORM\Entity
```

##### Map $entity in the entity map

Returns the given entity or an entity that previously got mapped. This is useful to work in every function with
the same object.

```php
$user = $enitityManager->map(new User(['id' => 42]));
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$update` | **boolean**  | Update the entity map |
| `$class` | **string**  | Overwrite the class |



#### ORM\Testing\EntityManagerMock::observe

```php
public function observe(
    string $class, \ORM\?ObserverInterface $observer = null
): \ORM\?CallbackObserver
```

##### Observe $class using $observer

If AbstractObserver is omitted it returns a new CallbackObserver. Usage example:
```php
$em->observe(User::class)
    ->on('inserted', function (User $user) { ... })
    ->on('deleted', function (User $user) { ... });
```

For more information about model events please consult the [documentation](https://tflori.github.io/

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\?CallbackObserver**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$observer` | **\ORM\?ObserverInterface**  |  |



#### ORM\Testing\EntityManagerMock::retrieve

```php
public function retrieve( string $class, array $primaryKey ): \ORM\Entity|null
```

##### Retrieve an entity by $primaryKey



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity|null**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$primaryKey` | **array**  |  |



#### ORM\Testing\EntityManagerMock::setConnection

```php
public function setConnection( $connection )
```

##### Add connection after instantiation

The connection can be an array of parameters for DbConfig::__construct(), a callable function that returns a PDO
instance, an instance of DbConfig or a PDO instance itself.

When it is not a PDO instance the connection get established on first use.

**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$connection` | **mixed**  | A configuration for (or a) PDO instance |



#### ORM\Testing\EntityManagerMock::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  | One of OPT_* constants |
| `$value` | **mixed**  |  |



#### ORM\Testing\EntityManagerMock::setResolver

```php
public static function setResolver( callable $resolver )
```

##### Overwrite the functionality of ::getInstance($class) by $resolver($class)



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$resolver` | **callable**  |  |



#### ORM\Testing\EntityManagerMock::sync

```php
public function sync( \ORM\Entity $entity, boolean $reset = false ): boolean
```

##### Synchronizing $entity with database

If $reset is true it also calls reset() on $entity.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$reset` | **boolean**  | Reset entities current data |



#### ORM\Testing\EntityManagerMock::useBulkInserts

```php
public function useBulkInserts(
    string $class, integer $limit = 20
): \ORM\BulkInsert
```

##### Force $class to use bulk insert.

At the end you should call finish bulk insert otherwise you may loose data.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\BulkInsert**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$limit` | **integer**  | Maximum number of rows per insert |





---

### ORM\Dbal\Type\Enum

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Enum data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$allowedValues` | **array&lt;string>** |  |



#### Methods

* [__construct](#ormdbaltypeenum__construct) Set constructor
* [factory](#ormdbaltypeenumfactory) Returns a new Type object
* [fits](#ormdbaltypeenumfits) Check if this type fits to $columnDefinition
* [getAllowedValues](#ormdbaltypeenumgetallowedvalues) 
* [validate](#ormdbaltypeenumvalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Enum::__construct

```php
public function __construct( array<string> $allowedValues ): Enum
```

##### Set constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$allowedValues` | **array&lt;string>**  |  |



#### ORM\Dbal\Type\Enum::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Enum::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Enum::getAllowedValues

```php
public function getAllowedValues(): array<string>
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,string&gt;**
<br />



#### ORM\Dbal\Type\Enum::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Error

**Extends:** [ORM\Exception](#ormexception)


#### Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'UNKNOWN'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |



#### Methods

* [__construct](#ormdbalerror__construct) Error constructor

#### ORM\Dbal\Error::__construct

```php
public function __construct(
    array $params = array(), null $code = null, null $message = null, 
    \ORM\Dbal\Error $previous = null
): Error
```

##### Error constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$params` | **array**  |  |
| `$code` | **null**  |  |
| `$message` | **null**  |  |
| `$previous` | **Error**  |  |





---

### ORM\Event








#### Constants

| Name | Value |
|------|-------|
| NAME | `'event'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | **Entity** |  |
| **protected** | `$data` | **array** |  |



#### Methods

* [__construct](#ormevent__construct) 
* [__get](#ormevent__get) 

#### ORM\Event::__construct

```php
public function __construct( \ORM\Entity $entity ): Event
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **Entity**  |  |



#### ORM\Event::__get

```php
public function __get( $name )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` |   |  |





---

### ORM\Exception

**Extends:** [](#)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Event\Fetched

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'fetched'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |
| **protected** | `$rawData` | **array** |  |



#### Methods

* [__construct](#ormeventfetched__construct) 
* [__get](#ormeventfetched__get) 

#### ORM\Event\Fetched::__construct

```php
public function __construct( \ORM\Entity $entity, array $rawData ): Fetched
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$rawData` | **array**  |  |



#### ORM\Event\Fetched::__get

```php
public function __get( $name )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` |   |  |





---

### ORM\Entity\GeneratesPrimaryKeys



#### Interface GeneratesPrimaryKeys

Describes a class that generates primary keys in the protected method generatePrimaryKey()







---

### ORM\Exception\IncompletePrimaryKey

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Event\Inserted

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'inserted'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Event\Inserting

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'inserting'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Exception\InvalidArgument

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exception\InvalidConfiguration

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\InvalidJson

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### InvalidJson Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'INVALID_JSON'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Exception\InvalidName

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exception\InvalidRelation

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Type\Json

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Json data type








#### Methods

* [factory](#ormdbaltypejsonfactory) Returns a new Type object
* [fits](#ormdbaltypejsonfits) Check if this type fits to $columnDefinition
* [validate](#ormdbaltypejsonvalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Json::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Json::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Json::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Relation\ManyToMany

**Extends:** [ORM\Relation](#ormrelation)


#### ManyToMany Relation






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

```php
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

```php
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

```php
public function addRelated(
    \ORM\Entity $self, array $entities, \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey** or **\ORM\Exception\InvalidRelation** or **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::convertShort

```php
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\ManyToMany::createRelation

```php
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

```php
public function deleteRelated(
    \ORM\Entity $self, array $entities, \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey** or **\ORM\Exception\InvalidRelation** or **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::fetch

```php
public function fetch(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::fetchAll

```php
public function fetchAll(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\ManyToMany::getClass

```php
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\ManyToMany::getForeignKey

```php
protected function getForeignKey( \ORM\Entity $self, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\ManyToMany::getOpponent

```php
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\ManyToMany::getReference

```php
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\ManyToMany::getTable

```php
public function getTable(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\ManyToMany::setRelated

```php
public function setRelated( \ORM\Entity $self, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Dbal\Mysql

**Extends:** [ORM\Dbal\Dbal](#ormdbaldbal)


#### Database abstraction for MySQL databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$typeMapping` | **array** |  |
| **protected static** | `$compositeWhereInTemplate` |  |  |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** |  |
| **protected** | `$quotingCharacter` | **string** |  |
| **protected** | `$identifierDivider` | **string** |  |
| **protected** | `$booleanTrue` | **string** |  |
| **protected** | `$booleanFalse` | **string** |  |



#### Methods

* [__construct](#ormdbalmysql__construct) Dbal constructor.
* [assertSameType](#ormdbalmysqlassertsametype) 
* [buildCompositeWhereInStatement](#ormdbalmysqlbuildcompositewhereinstatement) Build a where in statement for composite primary keys
* [buildInsertStatement](#ormdbalmysqlbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalmysqldelete) Delete $entity from database
* [describe](#ormdbalmysqldescribe) Describe a table
* [escapeBoolean](#ormdbalmysqlescapeboolean) Escape a boolean for query
* [escapeDateTime](#ormdbalmysqlescapedatetime) Escape a date time object for query
* [escapeDouble](#ormdbalmysqlescapedouble) Escape a double for Query
* [escapeIdentifier](#ormdbalmysqlescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeInteger](#ormdbalmysqlescapeinteger) Escape an integer for query
* [escapeNULL](#ormdbalmysqlescapenull) Escape NULL for query
* [escapeString](#ormdbalmysqlescapestring) Escape a string for query
* [escapeValue](#ormdbalmysqlescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalmysqlextractparenthesis) Extract content from parenthesis in $type
* [insert](#ormdbalmysqlinsert) Insert $entities into database
* [insertAndSync](#ormdbalmysqlinsertandsync) Insert $entities and update with default values from database
* [insertAndSyncWithAutoInc](#ormdbalmysqlinsertandsyncwithautoinc) Insert $entities and sync with auto increment primary key
* [normalizeColumnDefinition](#ormdbalmysqlnormalizecolumndefinition) Normalize a column definition
* [normalizeType](#ormdbalmysqlnormalizetype) Normalize $type
* [setOption](#ormdbalmysqlsetoption) Set $option to $value
* [syncInserted](#ormdbalmysqlsyncinserted) Sync the $entities after insert
* [updateAutoincrement](#ormdbalmysqlupdateautoincrement) Update the autoincrement value

#### ORM\Dbal\Mysql::__construct

```php
public function __construct(
    \ORM\EntityManager $entityManager, array $options = array()
): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |
| `$options` | **array**  |  |



#### ORM\Dbal\Mysql::assertSameType

```php
protected static function assertSameType(
    array<\ORM\Entity> $entities
): boolean
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Mysql::buildCompositeWhereInStatement

```php
protected function buildCompositeWhereInStatement(
    array $cols, array $entities
): string
```

##### Build a where in statement for composite primary keys



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cols` | **array**  |  |
| `$entities` | **array**  |  |



#### ORM\Dbal\Mysql::buildInsertStatement

```php
protected function buildInsertStatement(
    \ORM\Entity $entity, array<\ORM\Entity> $entities
): string
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
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Mysql::delete

```php
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

```php
public function describe(
    string $table
): \ORM\Dbal\Table|array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Table|array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$table` | **string**  |  |



#### ORM\Dbal\Mysql::escapeBoolean

```php
protected function escapeBoolean( boolean $value ): string
```

##### Escape a boolean for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **boolean**  |  |



#### ORM\Dbal\Mysql::escapeDateTime

```php
protected function escapeDateTime( \DateTime $value ): mixed
```

##### Escape a date time object for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **\DateTime**  |  |



#### ORM\Dbal\Mysql::escapeDouble

```php
protected function escapeDouble( double $value ): string
```

##### Escape a double for Query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **double**  |  |



#### ORM\Dbal\Mysql::escapeIdentifier

```php
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



#### ORM\Dbal\Mysql::escapeInteger

```php
protected function escapeInteger( integer $value ): string
```

##### Escape an integer for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **integer**  |  |



#### ORM\Dbal\Mysql::escapeNULL

```php
protected function escapeNULL(): string
```

##### Escape NULL for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Mysql::escapeString

```php
protected function escapeString( string $value ): string
```

##### Escape a string for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **string**  |  |



#### ORM\Dbal\Mysql::escapeValue

```php
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Mysql::extractParenthesis

```php
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



#### ORM\Dbal\Mysql::insert

```php
public function insert( \ORM\Entity $entities ): boolean
```

##### Insert $entities into database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::insertAndSync

```php
public function insertAndSync( \ORM\Entity $entities ): boolean
```

##### Insert $entities and update with default values from database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::insertAndSyncWithAutoInc

```php
public function insertAndSyncWithAutoInc(
    \ORM\Entity $entities
): integer|boolean
```

##### Insert $entities and sync with auto increment primary key

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer|boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::normalizeColumnDefinition

```php
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

```php
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



#### ORM\Dbal\Mysql::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Dbal\Mysql::syncInserted

```php
protected function syncInserted( \ORM\Entity $entities )
```

##### Sync the $entities after insert



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Mysql::updateAutoincrement

```php
protected function updateAutoincrement( \ORM\Entity $entity, integer $value )
```

##### Update the autoincrement value



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$value` | **integer &#124; string**  |  |





---

### ORM\Namer



#### Namer is for naming errors, columns, tables and methods

Namer is an artificial word and is more a name giver. We just don't wanted to write so much.




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$tableNameTemplate` | **string** | The template to use to calculate the table name. |
| **protected** | `$tableNameScheme` | **string** | The naming scheme to use for table names. |
| **protected** | `$tableNames` | **array&lt;string>** |  |
| **protected** | `$columnNames` | **array&lt;string[]>** |  |
| **protected** | `$columnNameScheme` | **string** | The naming scheme to use for column names. |
| **protected** | `$methodNameScheme` | **string** | The naming scheme used for method names. |
| **protected** | `$attributeNameScheme` | **string** | The naming scheme used for attributes. |



#### Methods

* [__construct](#ormnamer__construct) Namer constructor.
* [arrayToString](#ormnamerarraytostring) Convert array to string using indexes defined by $accessor
* [forceNamingScheme](#ormnamerforcenamingscheme) Enforce $namingScheme to $name
* [getAttributeName](#ormnamergetattributename) Get the attribute name with $namingScheme or default naming scheme
* [getColumnName](#ormnamergetcolumnname) Get the column name with $namingScheme or default naming scheme
* [getMethodName](#ormnamergetmethodname) Get the method name with $namingScheme or default naming scheme
* [getTableName](#ormnamergettablename) Get the table name for $reflection
* [getValue](#ormnamergetvalue) Get the value for $attribute from $values using $arrayGlue
* [setOption](#ormnamersetoption) Set $option to $value
* [substitute](#ormnamersubstitute) Substitute a $template with $values

#### ORM\Namer::__construct

```php
public function __construct( array $options = array() ): Namer
```

##### Namer constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$options` | **array**  |  |



#### ORM\Namer::arrayToString

```php
protected function arrayToString(
    array $array, string $accessor, string $glue
): string
```

##### Convert array to string using indexes defined by $accessor



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$array` | **array**  |  |
| `$accessor` | **string**  |  |
| `$glue` | **string**  |  |



#### ORM\Namer::forceNamingScheme

```php
public function forceNamingScheme( string $name, string $namingScheme ): string
```

##### Enforce $namingScheme to $name

Supported naming schemes: snake_case, snake_lower, SNAKE_UPPER, Snake_Ucfirst, camelCase, StudlyCaps, lower
and UPPER.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  | The name of the var / column |
| `$namingScheme` | **string**  | The naming scheme to use |



#### ORM\Namer::getAttributeName

```php
public function getAttributeName(
    string $name, $prefix = null, string $namingScheme = null
): string
```

##### Get the attribute name with $namingScheme or default naming scheme



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$prefix` |   |  |
| `$namingScheme` | **string**  |  |



#### ORM\Namer::getColumnName

```php
public function getColumnName(
    string $class, string $attribute, string $prefix = null, 
    string $namingScheme = null
): string
```

##### Get the column name with $namingScheme or default naming scheme



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$attribute` | **string**  |  |
| `$prefix` | **string**  |  |
| `$namingScheme` | **string**  |  |



#### ORM\Namer::getMethodName

```php
public function getMethodName(
    string $name, string $namingScheme = null
): string
```

##### Get the method name with $namingScheme or default naming scheme



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$namingScheme` | **string**  |  |



#### ORM\Namer::getTableName

```php
public function getTableName(
    string $class, string $template = null, string $namingScheme = null
): string
```

##### Get the table name for $reflection



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidName**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$template` | **string**  |  |
| `$namingScheme` | **string**  |  |



#### ORM\Namer::getValue

```php
protected function getValue(
    string $attribute, array $values, string $arrayGlue
): string
```

##### Get the value for $attribute from $values using $arrayGlue



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$attribute` | **string**  | The key for $values |
| `$values` | **array**  |  |
| `$arrayGlue` | **string**  |  |



#### ORM\Namer::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Namer::substitute

```php
public function substitute(
    string $template, array $values = array(), string $arrayGlue = ', '
): string
```

##### Substitute a $template with $values

$values is a key value pair array. The value should be a string or an array o

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$template` | **string**  |  |
| `$values` | **array**  |  |
| `$arrayGlue` | **string**  |  |





---

### ORM\Dbal\Error\NoBoolean

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NoBoolean Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NO_BOOLEAN'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Exception\NoConnection

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\NoDateTime

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NoDateTime Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NO_DATETIME'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Exception\NoEntity

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exception\NoEntityManager

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\NoNumber

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NoNumber Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NO_NUMBER'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Exception\NoOperator

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\NoString

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NoString Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NO_STRING'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Dbal\Error\NotAllowed

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NotAllowed Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NOT_ALLOWED'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Dbal\Error\NoTime

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NoTime Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NO_TIME'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Exception\NotJoined

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\NotNullable

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NotNullable Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NOT_NULLABLE'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |



#### Methods

* [__construct](#ormdbalerrornotnullable__construct) Error constructor

#### ORM\Dbal\Error\NotNullable::__construct

```php
public function __construct( \ORM\Dbal\Column $column ): NotNullable
```

##### Error constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **\ORM\Dbal\Column**  |  |





---

### ORM\Exception\NotScalar

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Dbal\Error\NotValid

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### NotValid Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'NOT_VALID'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |



#### Methods

* [__construct](#ormdbalerrornotvalid__construct) NotValid constructor

#### ORM\Dbal\Error\NotValid::__construct

```php
public function __construct(
    \ORM\Dbal\Column $column, \ORM\Dbal\Error $previous
): NotValid
```

##### NotValid constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **\ORM\Dbal\Column**  | The column that got a not valid error |
| `$previous` | **\ORM\Dbal\Error**  | The error from validate |





---

### ORM\Dbal\Type\Number

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### Float, double and decimal data type








#### Methods

* [factory](#ormdbaltypenumberfactory) Returns a new Type object
* [fits](#ormdbaltypenumberfits) Check if this type fits to $columnDefinition
* [validate](#ormdbaltypenumbervalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Number::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Number::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Number::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\ObserverInterface



#### AbstractObserver for entity events

When a handler returns false it will cancel other event handlers and if
applicable stops the execution (saving, inserting, updating and deleting
can be canceled).






#### Methods

* [deleted](#ormobserverinterfacedeleted) Gets called after an entity got deleted.
* [deleting](#ormobserverinterfacedeleting) Gets called before an entity gets deleted.
* [fetched](#ormobserverinterfacefetched) Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.
* [inserted](#ormobserverinterfaceinserted) Gets called after an entity gets inserted.
* [inserting](#ormobserverinterfaceinserting) Gets Called before an entity gets inserted.
* [saved](#ormobserverinterfacesaved) Gets called after an entity got saved.
* [saving](#ormobserverinterfacesaving) Gets called before an entity gets saved.
* [updated](#ormobserverinterfaceupdated) Gets called after an entity got updated.
* [updating](#ormobserverinterfaceupdating) Gets called before an entity gets updated.

#### ORM\ObserverInterface::deleted

```php
public function deleted( \ORM\Event\Deleted $event ): boolean
```

##### Gets called after an entity got deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Deleted**  |  |



#### ORM\ObserverInterface::deleting

```php
public function deleting( \ORM\Event\Deleting $event ): boolean
```

##### Gets called before an entity gets deleted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Deleting**  |  |



#### ORM\ObserverInterface::fetched

```php
public function fetched( \ORM\Event\Fetched $event ): boolean
```

##### Gets called when ever an Entity is fetched from an EntityFetcher or
with the parameter $fromDatabase = true.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Fetched**  |  |



#### ORM\ObserverInterface::inserted

```php
public function inserted( \ORM\Event\Inserted $event ): boolean
```

##### Gets called after an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Inserted**  |  |



#### ORM\ObserverInterface::inserting

```php
public function inserting( \ORM\Event\Inserting $event ): boolean
```

##### Gets Called before an entity gets inserted.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Inserting**  |  |



#### ORM\ObserverInterface::saved

```php
public function saved( \ORM\Event\Saved $event ): boolean
```

##### Gets called after an entity got saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Saved**  |  |



#### ORM\ObserverInterface::saving

```php
public function saving( \ORM\Event\Saving $event ): boolean
```

##### Gets called before an entity gets saved.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Saving**  |  |



#### ORM\ObserverInterface::updated

```php
public function updated( \ORM\Event\Updated $event ): boolean
```

##### Gets called after an entity got updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Updated**  |  |



#### ORM\ObserverInterface::updating

```php
public function updating( \ORM\Event\Updating $event ): boolean
```

##### Gets called before an entity gets updated.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$event` | **Event\Updating**  |  |





---

### ORM\Relation\OneToMany

**Extends:** [ORM\Relation](#ormrelation)


#### OneToMany Relation






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

```php
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

```php
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

```php
public function addRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::convertShort

```php
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToMany::createRelation

```php
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

```php
public function deleteRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::fetch

```php
public function fetch(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
): mixed
```

##### Fetch the relation

Runs fetch on the EntityManager and returns its result.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **mixed**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::fetchAll

```php
public function fetchAll(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToMany::getClass

```php
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\OneToMany::getForeignKey

```php
protected function getForeignKey( \ORM\Entity $self, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\OneToMany::getOpponent

```php
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\OneToMany::getReference

```php
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\OneToMany::setRelated

```php
public function setRelated( \ORM\Entity $self, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Relation\OneToOne

**Extends:** [ORM\Relation\OneToMany](#ormrelationonetomany)


#### OneToOne Relation






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

```php
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

```php
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

```php
public function addRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::convertShort

```php
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\OneToOne::createRelation

```php
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

```php
public function deleteRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::fetch

```php
public function fetch(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::fetchAll

```php
public function fetchAll(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\OneToOne::getClass

```php
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\OneToOne::getForeignKey

```php
protected function getForeignKey( \ORM\Entity $self, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\OneToOne::getOpponent

```php
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\OneToOne::getReference

```php
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\OneToOne::setRelated

```php
public function setRelated( \ORM\Entity $self, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entity` | **\ORM\Entity &#124; null**  |  |





---

### ORM\Dbal\Other

**Extends:** [ORM\Dbal\Dbal](#ormdbaldbal)


#### Database abstraction for other databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$typeMapping` | **array** |  |
| **protected static** | `$compositeWhereInTemplate` |  |  |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** |  |
| **protected** | `$quotingCharacter` | **string** |  |
| **protected** | `$identifierDivider` | **string** |  |
| **protected** | `$booleanTrue` | **string** |  |
| **protected** | `$booleanFalse` | **string** |  |




---

### ORM\Relation\Owner

**Extends:** [ORM\Relation](#ormrelation)


#### Owner Relation






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

```php
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

```php
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

```php
public function addRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::convertShort

```php
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation\Owner::createRelation

```php
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

```php
public function deleteRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$entities` | **array&lt;\ORM\Entity>**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::fetch

```php
public function fetch(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::fetchAll

```php
public function fetchAll(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **\ORM\Entity**  |  |
| `$entityManager` | **\ORM\EntityManager**  |  |



#### ORM\Relation\Owner::getClass

```php
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation\Owner::getForeignKey

```php
protected function getForeignKey( \ORM\Entity $self, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation\Owner::getOpponent

```php
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation\Owner::getReference

```php
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation\Owner::setRelated

```php
public function setRelated( \ORM\Entity $self, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation** or **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **\ORM\Entity**  |  |
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

```php
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

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\Parenthesis::andWhere

```php
public function andWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\Parenthesis::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\Parenthesis::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\Parenthesis::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\Parenthesis::orWhere

```php
public function orWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\Parenthesis::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\Parenthesis::where

```php
public function where(
    string $column, string $operator = null, string $value = null
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
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

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\ParenthesisInterface::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\ParenthesisInterface::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\ParenthesisInterface::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
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

**Extends:** [ORM\Dbal\Dbal](#ormdbaldbal)


#### Database abstraction for PostgreSQL databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$typeMapping` | **array** |  |
| **protected static** | `$compositeWhereInTemplate` |  |  |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** |  |
| **protected** | `$quotingCharacter` | **string** |  |
| **protected** | `$identifierDivider` | **string** |  |
| **protected** | `$booleanTrue` |  |  |
| **protected** | `$booleanFalse` |  |  |



#### Methods

* [__construct](#ormdbalpgsql__construct) Dbal constructor.
* [assertSameType](#ormdbalpgsqlassertsametype) 
* [buildCompositeWhereInStatement](#ormdbalpgsqlbuildcompositewhereinstatement) Build a where in statement for composite primary keys
* [buildInsertStatement](#ormdbalpgsqlbuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalpgsqldelete) Delete $entity from database
* [describe](#ormdbalpgsqldescribe) Describe a table
* [escapeBoolean](#ormdbalpgsqlescapeboolean) Escape a boolean for query
* [escapeDateTime](#ormdbalpgsqlescapedatetime) Escape a date time object for query
* [escapeDouble](#ormdbalpgsqlescapedouble) Escape a double for Query
* [escapeIdentifier](#ormdbalpgsqlescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeInteger](#ormdbalpgsqlescapeinteger) Escape an integer for query
* [escapeNULL](#ormdbalpgsqlescapenull) Escape NULL for query
* [escapeString](#ormdbalpgsqlescapestring) Escape a string for query
* [escapeValue](#ormdbalpgsqlescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalpgsqlextractparenthesis) Extract content from parenthesis in $type
* [insert](#ormdbalpgsqlinsert) Insert $entities into database
* [insertAndSync](#ormdbalpgsqlinsertandsync) Insert $entities and update with default values from database
* [insertAndSyncWithAutoInc](#ormdbalpgsqlinsertandsyncwithautoinc) Insert $entities and sync with auto increment primary key
* [normalizeType](#ormdbalpgsqlnormalizetype) Normalize $type
* [setOption](#ormdbalpgsqlsetoption) Set $option to $value
* [syncInserted](#ormdbalpgsqlsyncinserted) Sync the $entities after insert
* [updateAutoincrement](#ormdbalpgsqlupdateautoincrement) Update the autoincrement value

#### ORM\Dbal\Pgsql::__construct

```php
public function __construct(
    \ORM\EntityManager $entityManager, array $options = array()
): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |
| `$options` | **array**  |  |



#### ORM\Dbal\Pgsql::assertSameType

```php
protected static function assertSameType(
    array<\ORM\Entity> $entities
): boolean
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Pgsql::buildCompositeWhereInStatement

```php
protected function buildCompositeWhereInStatement(
    array $cols, array $entities
): string
```

##### Build a where in statement for composite primary keys



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cols` | **array**  |  |
| `$entities` | **array**  |  |



#### ORM\Dbal\Pgsql::buildInsertStatement

```php
protected function buildInsertStatement(
    \ORM\Entity $entity, array<\ORM\Entity> $entities
): string
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
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Pgsql::delete

```php
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

```php
public function describe(
    $schemaTable
): \ORM\Dbal\Table|array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Table|array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$schemaTable` |   |  |



#### ORM\Dbal\Pgsql::escapeBoolean

```php
protected function escapeBoolean( boolean $value ): string
```

##### Escape a boolean for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **boolean**  |  |



#### ORM\Dbal\Pgsql::escapeDateTime

```php
protected function escapeDateTime( \DateTime $value ): mixed
```

##### Escape a date time object for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **\DateTime**  |  |



#### ORM\Dbal\Pgsql::escapeDouble

```php
protected function escapeDouble( double $value ): string
```

##### Escape a double for Query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **double**  |  |



#### ORM\Dbal\Pgsql::escapeIdentifier

```php
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



#### ORM\Dbal\Pgsql::escapeInteger

```php
protected function escapeInteger( integer $value ): string
```

##### Escape an integer for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **integer**  |  |



#### ORM\Dbal\Pgsql::escapeNULL

```php
protected function escapeNULL(): string
```

##### Escape NULL for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Pgsql::escapeString

```php
protected function escapeString( string $value ): string
```

##### Escape a string for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **string**  |  |



#### ORM\Dbal\Pgsql::escapeValue

```php
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Pgsql::extractParenthesis

```php
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



#### ORM\Dbal\Pgsql::insert

```php
public function insert( \ORM\Entity $entities ): boolean
```

##### Insert $entities into database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::insertAndSync

```php
public function insertAndSync( \ORM\Entity $entities ): boolean
```

##### Insert $entities and update with default values from database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::insertAndSyncWithAutoInc

```php
public function insertAndSyncWithAutoInc(
    \ORM\Entity $entities
): integer|boolean
```

##### Insert $entities and sync with auto increment primary key

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer|boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::normalizeType

```php
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



#### ORM\Dbal\Pgsql::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Dbal\Pgsql::syncInserted

```php
protected function syncInserted( \ORM\Entity $entities )
```

##### Sync the $entities after insert



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Pgsql::updateAutoincrement

```php
protected function updateAutoincrement( \ORM\Entity $entity, integer $value )
```

##### Update the autoincrement value



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$value` | **integer &#124; string**  |  |





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

```php
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

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\QueryBuilder::andWhere

```php
public function andWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilder::buildExpression

```php
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

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilder::column

```php
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

```php
public function columns( array $columns = null ): static
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\QueryBuilder\QueryBuilder::convertPlaceholders

```php
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces question marks in $expression with $args



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Arguments for placeholders |



#### ORM\QueryBuilder\QueryBuilder::fullJoin

```php
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::getDefaultOperator

```php
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\QueryBuilder\QueryBuilder::getEntityManager

```php
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



#### ORM\QueryBuilder\QueryBuilder::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilder::getQuery

```php
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilder::groupBy

```php
public function groupBy( string $column, array $args = array() ): static
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::join

```php
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::leftJoin

```php
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::limit

```php
public function limit( integer $limit ): static
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\QueryBuilder\QueryBuilder::modifier

```php
public function modifier( string $modifier ): static
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\QueryBuilder\QueryBuilder::offset

```php
public function offset( integer $offset ): static
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\QueryBuilder\QueryBuilder::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): static
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\QueryBuilder::orWhere

```php
public function orWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilder::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\QueryBuilder::rightJoin

```php
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilder::where

```php
public function where(
    string $column, string $operator = null, string $value = null
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
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

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::andWhere

```php
public function andWhere(
    string $column, string $operator = '', string $value = ''
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::column

```php
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

```php
public function columns( $columns = null ): static
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` |   |  |



#### ORM\QueryBuilder\QueryBuilderInterface::fullJoin

```php
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::getQuery

```php
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::groupBy

```php
public function groupBy( string $column, array $args = array() ): static
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::join

```php
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::leftJoin

```php
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::limit

```php
public function limit( integer $limit ): static
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\QueryBuilder\QueryBuilderInterface::modifier

```php
public function modifier( string $modifier ): static
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\QueryBuilder\QueryBuilderInterface::offset

```php
public function offset( integer $offset ): static
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\QueryBuilder\QueryBuilderInterface::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): static
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\QueryBuilder\QueryBuilderInterface::orWhere

```php
public function orWhere(
    string $column, string $operator = '', string $value = ''
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\QueryBuilder\QueryBuilderInterface::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



**See Also:**

* \ORM\QueryBuilder\ParenthesisInterface::andWhere() 
#### ORM\QueryBuilder\QueryBuilderInterface::rightJoin

```php
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\QueryBuilder\QueryBuilderInterface::where

```php
public function where(
    string $column, string $operator = '', string $value = ''
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
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



#### Base Relation





#### Constants

| Name | Value |
|------|-------|
| OPT_CLASS | `'class'` |
| OPT_REFERENCE | `'reference'` |
| OPT_CARDINALITY | `'cardinality'` |
| OPT_OPPONENT | `'opponent'` |
| OPT_TABLE | `'table'` |
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

```php
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

```php
public function addRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Add $entities to association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **Entity**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::convertShort

```php
protected static function convertShort( string $name, array $relDef ): array
```

##### Converts short form to assoc form



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidConfiguration**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string**  |  |
| `$relDef` | **array**  |  |



#### ORM\Relation::createRelation

```php
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

```php
public function deleteRelated(
    \ORM\Entity $self, array<\ORM\Entity> $entities, 
    \ORM\EntityManager $entityManager
)
```

##### Delete $entities from association table



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **Entity**  |  |
| `$entities` | **array&lt;Entity>**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::fetch

```php
abstract public function fetch(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **Entity**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::fetchAll

```php
public function fetchAll(
    \ORM\Entity $self, \ORM\EntityManager $entityManager
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
| `$self` | **Entity**  |  |
| `$entityManager` | **EntityManager**  |  |



#### ORM\Relation::getClass

```php
public function getClass(): string
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Relation::getForeignKey

```php
protected function getForeignKey( \ORM\Entity $self, array $reference ): array
```

##### Get the foreign key for the given reference



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **array**
<br />**Throws:** this method may throw **\ORM\Exception\IncompletePrimaryKey**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **Entity**  |  |
| `$reference` | **array**  |  |



#### ORM\Relation::getOpponent

```php
public function getOpponent(): \ORM\Relation
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Relation**
<br />



#### ORM\Relation::getReference

```php
public function getReference(): array
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />



#### ORM\Relation::setRelated

```php
public function setRelated( \ORM\Entity $self, \ORM\Entity $entity = null )
```

##### Set the relation to $entity



**Visibility:** this method is **public**.
<br />
**Throws:** this method may throw **\ORM\Exception\InvalidRelation**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$self` | **Entity**  |  |
| `$entity` | **Entity &#124; null**  |  |





---

### ORM\Testing\EntityFetcherMock\Result

**Extends:** [ORM\EntityFetcher](#ormentityfetcher)


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
| **protected** | `$class` | **string &#124;  \ ORM \ Entity** | The entity class that we want to fetch |
| **protected** | `$result` | ** \ PDOStatement** | The result object from PDO |
| **protected** | `$query` | **string &#124;  \ ORM \ QueryBuilder \ QueryBuilderInterface** | The query to execute (overwrites other settings) |
| **protected** | `$classMapping` | **array&lt;string[]>** | The class to alias mapping and vise versa |
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
| **protected** | `$where` | **array&lt;string>** | Where conditions get concatenated with space |
| **protected** | `$onClose` | **callable** | Callback to close the parenthesis |
| **protected** | `$parent` | ** \ ORM \ QueryBuilder \ ParenthesisInterface** | Parent parenthesis or query |
| **protected** | `$entities` | **array&lt; \ ORM \ Entity>** |  |
| **protected** | `$regularExpressions` | **array&lt;string>** |  |



#### Methods

* [__construct](#ormtestingentityfetchermockresult__construct) Constructor
* [addEntities](#ormtestingentityfetchermockresultaddentities) Add entities to the result
* [all](#ormtestingentityfetchermockresultall) Fetch an array of entities
* [andParenthesis](#ormtestingentityfetchermockresultandparenthesis) Add a parenthesis with AND
* [andWhere](#ormtestingentityfetchermockresultandwhere) Add a where condition with AND.
* [buildExpression](#ormtestingentityfetchermockresultbuildexpression) 
* [close](#ormtestingentityfetchermockresultclose) Close parenthesis
* [column](#ormtestingentityfetchermockresultcolumn) Add $column
* [columns](#ormtestingentityfetchermockresultcolumns) Set $columns
* [compare](#ormtestingentityfetchermockresultcompare) Check if $fetcher matches the current query
* [convertPlaceholders](#ormtestingentityfetchermockresultconvertplaceholders) Replaces question marks in $expression with $args
* [count](#ormtestingentityfetchermockresultcount) Get the count of the resulting items
* [createRelatedJoin](#ormtestingentityfetchermockresultcreaterelatedjoin) Create the join with $join type
* [fullJoin](#ormtestingentityfetchermockresultfulljoin) Full (outer) join $tableName with $options
* [getDefaultOperator](#ormtestingentityfetchermockresultgetdefaultoperator) 
* [getEntities](#ormtestingentityfetchermockresultgetentities) Get the entities for this result
* [getEntityManager](#ormtestingentityfetchermockresultgetentitymanager) 
* [getExpression](#ormtestingentityfetchermockresultgetexpression) Get the expression
* [getQuery](#ormtestingentityfetchermockresultgetquery) Get the query / select statement
* [getStatement](#ormtestingentityfetchermockresultgetstatement) Query database and return result
* [groupBy](#ormtestingentityfetchermockresultgroupby) Group By $column
* [join](#ormtestingentityfetchermockresultjoin) (Inner) join $tableName with $options
* [joinRelated](#ormtestingentityfetchermockresultjoinrelated) Join $relation
* [leftJoin](#ormtestingentityfetchermockresultleftjoin) Left (outer) join $tableName with $options
* [leftJoinRelated](#ormtestingentityfetchermockresultleftjoinrelated) Left outer join $relation
* [limit](#ormtestingentityfetchermockresultlimit) Set $limit
* [matches](#ormtestingentityfetchermockresultmatches) Add a regular expression that has to match
* [modifier](#ormtestingentityfetchermockresultmodifier) Add $modifier
* [offset](#ormtestingentityfetchermockresultoffset) Set $offset
* [one](#ormtestingentityfetchermockresultone) Fetch one entity
* [orderBy](#ormtestingentityfetchermockresultorderby) Order By $column in $direction
* [orParenthesis](#ormtestingentityfetchermockresultorparenthesis) Add a parenthesis with OR
* [orWhere](#ormtestingentityfetchermockresultorwhere) Add a where condition with OR.
* [parenthesis](#ormtestingentityfetchermockresultparenthesis) Alias for andParenthesis
* [rightJoin](#ormtestingentityfetchermockresultrightjoin) Right (outer) join $tableName with $options
* [setQuery](#ormtestingentityfetchermockresultsetquery) Set a raw query or use different QueryBuilder
* [where](#ormtestingentityfetchermockresultwhere) Alias for andWhere

#### ORM\Testing\EntityFetcherMock\Result::__construct

```php
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
| `$parent` | **\ORM\QueryBuilder\ParenthesisInterface**  | Parent where createWhereCondition get executed |



#### ORM\Testing\EntityFetcherMock\Result::addEntities

```php
public function addEntities( array<\ORM\Entity> $entities ): $this
```

##### Add entities to the result



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Testing\EntityFetcherMock\Result::all

```php
public function all( integer $limit ): array<\ORM\Entity>
```

##### Fetch an array of entities

When no $limit is set it fetches all entities in result set.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | Maximum number of entities to fetch |



#### ORM\Testing\EntityFetcherMock\Result::andParenthesis

```php
public function andParenthesis(): static
```

##### Add a parenthesis with AND



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock\Result::andWhere

```php
public function andWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with AND.

QueryBuilderInterface andWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->andWhere('name', '=' , 'John Doe');
$query->andWhere('name = ?', 'John Doe');
$query->andWhere('name', 'John Doe');
$query->andWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\Testing\EntityFetcherMock\Result::buildExpression

```php
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



#### ORM\Testing\EntityFetcherMock\Result::close

```php
public function close(): \ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface
```

##### Close parenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\QueryBuilder\QueryBuilderInterface|\ORM\QueryBuilder\ParenthesisInterface**
<br />



#### ORM\Testing\EntityFetcherMock\Result::column

```php
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



#### ORM\Testing\EntityFetcherMock\Result::columns

```php
public function columns( array $columns = null ): static
```

##### Set $columns



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array**  |  |



#### ORM\Testing\EntityFetcherMock\Result::compare

```php
public function compare( \ORM\EntityFetcher $fetcher ): integer
```

##### Check if $fetcher matches the current query

Returns the score for the given EntityFetcher. The more conditions match the higher the score:
- 0 = the query does not match one of the conditions
- 1 = no conditions required to match the query
- n = n-1 conditions matched the query

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$fetcher` | **\ORM\EntityFetcher**  |  |



#### ORM\Testing\EntityFetcherMock\Result::convertPlaceholders

```php
protected function convertPlaceholders(
    string $expression, array $args
): string
```

##### Replaces question marks in $expression with $args



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  | Expression with placeholders |
| `$args` | **array &#124; mixed**  | Arguments for placeholders |



#### ORM\Testing\EntityFetcherMock\Result::count

```php
public function count(): integer
```

##### Get the count of the resulting items



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\Testing\EntityFetcherMock\Result::createRelatedJoin

```php
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



#### ORM\Testing\EntityFetcherMock\Result::fullJoin

```php
public function fullJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Full (outer) join $tableName with $options

When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
can be set to false.

ATTENTION: here the default value of empty got changed - defaults to yes

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::getDefaultOperator

```php
private function getDefaultOperator( $value )
```




**Visibility:** this method is **private**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` |   |  |



#### ORM\Testing\EntityFetcherMock\Result::getEntities

```php
public function getEntities(): array<\ORM\Entity>
```

##### Get the entities for this result



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />



#### ORM\Testing\EntityFetcherMock\Result::getEntityManager

```php
public function getEntityManager(): \ORM\EntityManager
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\EntityManager**
<br />



#### ORM\Testing\EntityFetcherMock\Result::getExpression

```php
public function getExpression(): string
```

##### Get the expression

Returns the complete expression inside this parenthesis.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Testing\EntityFetcherMock\Result::getQuery

```php
public function getQuery(): string
```

##### Get the query / select statement

Builds the statement from current where conditions, joins, columns and so on.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Testing\EntityFetcherMock\Result::getStatement

```php
private function getStatement(): \PDOStatement|boolean
```

##### Query database and return result

Queries the database with current query and returns the resulted PDOStatement.

If query failed it returns false. It also stores this failed result and to change the query afterwards will not
change the result.

**Visibility:** this method is **private**.
<br />
 **Returns**: this method returns **\PDOStatement|boolean**
<br />



#### ORM\Testing\EntityFetcherMock\Result::groupBy

```php
public function groupBy( string $column, array $args = array() ): static
```

##### Group By $column

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for groups |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::join

```php
public function join(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### (Inner) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::joinRelated

```php
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



#### ORM\Testing\EntityFetcherMock\Result::leftJoin

```php
public function leftJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Left (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::leftJoinRelated

```php
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



#### ORM\Testing\EntityFetcherMock\Result::limit

```php
public function limit( integer $limit ): static
```

##### Set $limit

Limits the amount of rows fetched from database.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$limit` | **integer**  | The limit to set |



#### ORM\Testing\EntityFetcherMock\Result::matches

```php
public function matches( string $expression ): $this
```

##### Add a regular expression that has to match



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$expression` | **string**  |  |



#### ORM\Testing\EntityFetcherMock\Result::modifier

```php
public function modifier( string $modifier ): static
```

##### Add $modifier

Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$modifier` | **string**  |  |



#### ORM\Testing\EntityFetcherMock\Result::offset

```php
public function offset( integer $offset ): static
```

##### Set $offset

Changes the offset (only with limit) where fetching starts in the query.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **integer**  | The offset to set |



#### ORM\Testing\EntityFetcherMock\Result::one

```php
public function one(): \ORM\Entity
```

##### Fetch one entity

If there is no more entity in the result set it returns null.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity**
<br />



#### ORM\Testing\EntityFetcherMock\Result::orderBy

```php
public function orderBy(
    string $column, string $direction = self::DIRECTION_ASCENDING, 
    array $args = array()
): static
```

##### Order By $column in $direction

Optionally you can provide an expression in $column with question marks as placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression for order |
| `$direction` | **string**  | Direction (default: `ASC`) |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::orParenthesis

```php
public function orParenthesis(): static
```

##### Add a parenthesis with OR



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock\Result::orWhere

```php
public function orWhere(
    string $column, string $operator = null, string $value = null
): static
```

##### Add a where condition with OR.

QueryBuilderInterface orWhere($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->orWhere('name', '=' , 'John Doe');
$query->orWhere('name = ?', 'John Doe');
$query->orWhere('name', 'John Doe');
$query->orWhere('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |



#### ORM\Testing\EntityFetcherMock\Result::parenthesis

```php
public function parenthesis(): static
```

##### Alias for andParenthesis



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />



#### ORM\Testing\EntityFetcherMock\Result::rightJoin

```php
public function rightJoin(
    string $tableName, string $expression = '', string $alias = '', 
    array $args = array()
): static|\ORM\QueryBuilder\ParenthesisInterface
```

##### Right (outer) join $tableName with $options

When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
can be set to true.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static|\ORM\QueryBuilder\ParenthesisInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$tableName` | **string**  | Table to join |
| `$expression` | **string &#124; boolean**  | Expression, single column name or boolean to create an empty join |
| `$alias` | **string**  | Alias for the table |
| `$args` | **array**  | Arguments for expression |



#### ORM\Testing\EntityFetcherMock\Result::setQuery

```php
public function setQuery( string $query, array $args = null ): $this
```

##### Set a raw query or use different QueryBuilder

For easier use and against sql injection it allows question mark placeholders.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **$this**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string &#124; \ORM\QueryBuilder\QueryBuilderInterface**  | Raw query string or a QueryBuilderInterface |
| `$args` | **array**  | The arguments for placeholders |



#### ORM\Testing\EntityFetcherMock\Result::where

```php
public function where(
    string $column, string $operator = null, string $value = null
): static
```

##### Alias for andWhere

QueryBuilderInterface where($column[, $operator[, $value]]);

If $column has the same amount of question marks as $value - $value is the second parameter.

If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
the second parameter.

These calls are equal:

```php
$query->where('name', '=' , 'John Doe');
$query->where('name = ?', 'John Doe');
$query->where('name', 'John Doe');
$query->where('name = ?', ['John Doe']);
```

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **string**  | Column or expression with placeholders |
| `$operator` | **string &#124; array**  | Operator, value or array of values |
| `$value` | **string**  | Value (required when used with operator) |





---

### ORM\Testing\EntityFetcherMock\ResultRepository








#### Constants

| Name | Value |
|------|-------|
| RANDOM_KEY_MIN | `1000000000` |
| RANDOM_KEY_MAX | `1000999999` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$primaryKeyMap` | **array&lt; \ ORM \ Entity[]>** |  |
| **protected** | `$results` | **array&lt;Result[]>** |  |
| **protected** | `$em` | ** \ ORM \ EntityManager** |  |



#### Methods

* [__construct](#ormtestingentityfetchermockresultrepository__construct) ResultRepository constructor.
* [addEntity](#ormtestingentityfetchermockresultrepositoryaddentity) Add an entity to be fetched by primary key
* [addResult](#ormtestingentityfetchermockresultrepositoryaddresult) Create and add a EntityFetcherMock\Result for $class
* [buildChecksum](#ormtestingentityfetchermockresultrepositorybuildchecksum) Build a checksum from $primaryKey
* [completePrimaryKeys](#ormtestingentityfetchermockresultrepositorycompleteprimarykeys) Fill the primary keys of $entities
* [getResults](#ormtestingentityfetchermockresultrepositorygetresults) Get the results for $class and $query
* [retrieve](#ormtestingentityfetchermockresultrepositoryretrieve) Retrieve an entity by $primaryKey

#### ORM\Testing\EntityFetcherMock\ResultRepository::__construct

```php
public function __construct( \ORM\EntityManager $em ): ResultRepository
```

##### ResultRepository constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$em` | **\ORM\EntityManager**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::addEntity

```php
public function addEntity( \ORM\Entity $entity )
```

##### Add an entity to be fetched by primary key

The entity needs to have a primary key if not it will be filled with random values between RANDOM_KEY_MIN and
RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).

You can pass mocks from Entity too but we need to call `Entity::getPrimaryKey()`.

**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::addResult

```php
public function addResult(
    $class, \ORM\Entity $entities
): \ORM\Testing\EntityFetcherMock\Result|\Mockery\MockInterface
```

##### Create and add a EntityFetcherMock\Result for $class

As the results are mocked to come from the database they will also get a primary key if they don't have already.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Testing\EntityFetcherMock\Result|\Mockery\MockInterface**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` |   |  |
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::buildChecksum

```php
protected static function buildChecksum( array $primaryKey ): string
```

##### Build a checksum from $primaryKey



**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$primaryKey` | **array**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::completePrimaryKeys

```php
public static function completePrimaryKeys(
    \ORM\Entity $entities
): array<\ORM\Entity>
```

##### Fill the primary keys of $entities

If the primary key is incomplete the missing attributes will be filled with a random integer between
RANDOM_KEY_MIN and RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,\ORM\Entity&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::getResults

```php
public function getResults( string $class, \ORM\EntityFetcher $fetcher ): array
```

##### Get the results for $class and $query

The EntityFetcherMock\Result gets a quality for matching this query. Only the highest quality will be used.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$fetcher` | **\ORM\EntityFetcher**  |  |



#### ORM\Testing\EntityFetcherMock\ResultRepository::retrieve

```php
public function retrieve( string $class, array $primaryKey ): \ORM\Entity|null
```

##### Retrieve an entity by $primaryKey



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Entity|null**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **string**  |  |
| `$primaryKey` | **array**  |  |





---

### ORM\Event\Saved

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'saved'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |
| **protected** | `$originalEvent` | ** \ ORM \ Event** |  |



#### Methods

* [__construct](#ormeventsaved__construct) 
* [__get](#ormeventsaved__get) 

#### ORM\Event\Saved::__construct

```php
public function __construct( \ORM\Event $originalEvent ): Saved
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$originalEvent` | **\ORM\Event**  |  |



#### ORM\Event\Saved::__get

```php
public function __get( $name )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` |   |  |





---

### ORM\Event\Saving

**Extends:** [ORM\Event](#ormevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'saving'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Dbal\Type\Set

**Extends:** [ORM\Dbal\Type\Enum](#ormdbaltypeenum)


#### Set data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$allowedValues` | **array&lt;string>** |  |
| **protected** | `$type` |  |  |



#### Methods

* [__construct](#ormdbaltypeset__construct) Set constructor
* [factory](#ormdbaltypesetfactory) Returns a new Type object
* [fits](#ormdbaltypesetfits) Check if this type fits to $columnDefinition
* [getAllowedValues](#ormdbaltypesetgetallowedvalues) 
* [validate](#ormdbaltypesetvalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Set::__construct

```php
public function __construct( array<string> $allowedValues ): Enum
```

##### Set constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$allowedValues` | **array&lt;string>**  |  |



#### ORM\Dbal\Type\Set::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Set::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Set::getAllowedValues

```php
public function getAllowedValues(): array<string>
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **array&lt;mixed,string&gt;**
<br />



#### ORM\Dbal\Type\Set::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Sqlite

**Extends:** [ORM\Dbal\Dbal](#ormdbaldbal)


#### Database abstraction for SQLite databases






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected static** | `$typeMapping` | **array** |  |
| **protected static** | `$compositeWhereInTemplate` |  |  |
| **protected** | `$entityManager` | ** \ ORM \ EntityManager** |  |
| **protected** | `$quotingCharacter` | **string** |  |
| **protected** | `$identifierDivider` | **string** |  |
| **protected** | `$booleanTrue` | **string** |  |
| **protected** | `$booleanFalse` | **string** |  |



#### Methods

* [__construct](#ormdbalsqlite__construct) Dbal constructor.
* [assertSameType](#ormdbalsqliteassertsametype) 
* [buildCompositeWhereInStatement](#ormdbalsqlitebuildcompositewhereinstatement) Build a where in statement for composite primary keys
* [buildInsertStatement](#ormdbalsqlitebuildinsertstatement) Build the insert statement for $entity
* [delete](#ormdbalsqlitedelete) Delete $entity from database
* [describe](#ormdbalsqlitedescribe) Describe a table
* [escapeBoolean](#ormdbalsqliteescapeboolean) Escape a boolean for query
* [escapeDateTime](#ormdbalsqliteescapedatetime) Escape a date time object for query
* [escapeDouble](#ormdbalsqliteescapedouble) Escape a double for Query
* [escapeIdentifier](#ormdbalsqliteescapeidentifier) Returns $identifier quoted for use in a sql statement
* [escapeInteger](#ormdbalsqliteescapeinteger) Escape an integer for query
* [escapeNULL](#ormdbalsqliteescapenull) Escape NULL for query
* [escapeString](#ormdbalsqliteescapestring) Escape a string for query
* [escapeValue](#ormdbalsqliteescapevalue) Returns $value formatted to use in a sql statement.
* [extractParenthesis](#ormdbalsqliteextractparenthesis) Extract content from parenthesis in $type
* [hasCompositeKey](#ormdbalsqlitehascompositekey) Checks $rawColumns for a multiple primary key
* [insert](#ormdbalsqliteinsert) Insert $entities into database
* [insertAndSync](#ormdbalsqliteinsertandsync) Insert $entities and update with default values from database
* [insertAndSyncWithAutoInc](#ormdbalsqliteinsertandsyncwithautoinc) Insert $entities and sync with auto increment primary key
* [normalizeColumnDefinition](#ormdbalsqlitenormalizecolumndefinition) Normalize a column definition
* [normalizeType](#ormdbalsqlitenormalizetype) Normalize $type
* [setOption](#ormdbalsqlitesetoption) Set $option to $value
* [syncInserted](#ormdbalsqlitesyncinserted) Sync the $entities after insert
* [updateAutoincrement](#ormdbalsqliteupdateautoincrement) Update the autoincrement value

#### ORM\Dbal\Sqlite::__construct

```php
public function __construct(
    \ORM\EntityManager $entityManager, array $options = array()
): Dbal
```

##### Dbal constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entityManager` | **\ORM\EntityManager**  |  |
| `$options` | **array**  |  |



#### ORM\Dbal\Sqlite::assertSameType

```php
protected static function assertSameType(
    array<\ORM\Entity> $entities
): boolean
```




**Static:** this method is **static**.
<br />**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Sqlite::buildCompositeWhereInStatement

```php
protected function buildCompositeWhereInStatement(
    array $cols, array $entities
): string
```

##### Build a where in statement for composite primary keys



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cols` | **array**  |  |
| `$entities` | **array**  |  |



#### ORM\Dbal\Sqlite::buildInsertStatement

```php
protected function buildInsertStatement(
    \ORM\Entity $entity, array<\ORM\Entity> $entities
): string
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
| `$entities` | **array&lt;\ORM\Entity>**  |  |



#### ORM\Dbal\Sqlite::delete

```php
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

```php
public function describe(
    $schemaTable
): \ORM\Dbal\Table|array<\ORM\Dbal\Column>
```

##### Describe a table



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Table|array&lt;mixed,\ORM\Dbal\Column&gt;**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$schemaTable` |   |  |



#### ORM\Dbal\Sqlite::escapeBoolean

```php
protected function escapeBoolean( boolean $value ): string
```

##### Escape a boolean for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **boolean**  |  |



#### ORM\Dbal\Sqlite::escapeDateTime

```php
protected function escapeDateTime( \DateTime $value ): mixed
```

##### Escape a date time object for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **mixed**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **\DateTime**  |  |



#### ORM\Dbal\Sqlite::escapeDouble

```php
protected function escapeDouble( double $value ): string
```

##### Escape a double for Query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **double**  |  |



#### ORM\Dbal\Sqlite::escapeIdentifier

```php
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



#### ORM\Dbal\Sqlite::escapeInteger

```php
protected function escapeInteger( integer $value ): string
```

##### Escape an integer for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **integer**  |  |



#### ORM\Dbal\Sqlite::escapeNULL

```php
protected function escapeNULL(): string
```

##### Escape NULL for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />



#### ORM\Dbal\Sqlite::escapeString

```php
protected function escapeString( string $value ): string
```

##### Escape a string for query



**Visibility:** this method is **protected**.
<br />
 **Returns**: this method returns **string**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **string**  |  |



#### ORM\Dbal\Sqlite::escapeValue

```php
public function escapeValue( $value ): string
```

##### Returns $value formatted to use in a sql statement.



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **string**
<br />**Throws:** this method may throw **\ORM\Exception\NotScalar**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  | The variable that should be returned in SQL syntax |



#### ORM\Dbal\Sqlite::extractParenthesis

```php
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



#### ORM\Dbal\Sqlite::hasCompositeKey

```php
protected function hasCompositeKey( array $rawColumns ): boolean
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

```php
public function insert( \ORM\Entity $entities ): boolean
```

##### Insert $entities into database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::insertAndSync

```php
public function insertAndSync( \ORM\Entity $entities ): boolean
```

##### Insert $entities and update with default values from database

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />**Throws:** this method may throw **\ORM\Exception\InvalidArgument**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::insertAndSyncWithAutoInc

```php
public function insertAndSyncWithAutoInc(
    \ORM\Entity $entities
): integer|boolean
```

##### Insert $entities and sync with auto increment primary key

The entities have to be from same type otherwise a InvalidArgument will be thrown.

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer|boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::normalizeColumnDefinition

```php
protected function normalizeColumnDefinition(
    array $rawColumn, boolean $compositeKey = false
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
| `$compositeKey` | **boolean**  |  |



#### ORM\Dbal\Sqlite::normalizeType

```php
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



#### ORM\Dbal\Sqlite::setOption

```php
public function setOption( string $option, $value ): static
```

##### Set $option to $value



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$option` | **string**  |  |
| `$value` | **mixed**  |  |



#### ORM\Dbal\Sqlite::syncInserted

```php
protected function syncInserted( \ORM\Entity $entities )
```

##### Sync the $entities after insert



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entities` | **\ORM\Entity**  |  |



#### ORM\Dbal\Sqlite::updateAutoincrement

```php
protected function updateAutoincrement( \ORM\Entity $entity, integer $value )
```

##### Update the autoincrement value



**Visibility:** this method is **protected**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$value` | **integer &#124; string**  |  |





---

### ORM\Dbal\Table

**Extends:** [](#)


#### Table is basically an array of Columns






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$columns` | **array&lt;Column>** | The columns from this table |



#### Methods

* [__construct](#ormdbaltable__construct) Table constructor.
* [getColumn](#ormdbaltablegetcolumn) Get the Column object for $col
* [validate](#ormdbaltablevalidate) Validate $value for column $col.

#### ORM\Dbal\Table::__construct

```php
public function __construct( array<\ORM\Dbal\Column> $columns ): Table
```

##### Table constructor.



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columns` | **array&lt;Column>**  |  |



#### ORM\Dbal\Table::getColumn

```php
public function getColumn( string $col ): \ORM\Dbal\Column
```

##### Get the Column object for $col



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Column**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$col` | **string**  |  |



#### ORM\Dbal\Table::validate

```php
public function validate( string $col, $value ): boolean|\ORM\Dbal\Error
```

##### Validate $value for column $col.

Returns an array with at least

**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />**Throws:** this method may throw **\ORM\Exception\UnknownColumn**<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$col` | **string**  |  |
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Type\Text

**Extends:** [ORM\Dbal\Type\VarChar](#ormdbaltypevarchar)


#### Text data type

This is also the base type for any other data type




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$maxLength` | **integer** |  |
| **protected** | `$type` | **string** |  |




---

### ORM\Dbal\Type\Time

**Extends:** [ORM\Dbal\Type\DateTime](#ormdbaltypedatetime)


#### Time data type






#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$precision` | **integer** |  |
| **protected** | `$regex` | **string** |  |



#### Methods

* [__construct](#ormdbaltypetime__construct) DateTime constructor
* [factory](#ormdbaltypetimefactory) Returns a new Type object
* [fits](#ormdbaltypetimefits) Check if this type fits to $columnDefinition
* [getPrecision](#ormdbaltypetimegetprecision) 
* [validate](#ormdbaltypetimevalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\Time::__construct

```php
public function __construct( integer $precision = null ): Time
```

##### DateTime constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$precision` | **integer**  |  |



#### ORM\Dbal\Type\Time::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Time::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\Time::getPrecision

```php
public function getPrecision(): integer
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\Dbal\Type\Time::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Dbal\Error\TooLong

**Extends:** [ORM\Dbal\Error](#ormdbalerror)


#### TooLong Validation Error

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.



#### Constants

| Name | Value |
|------|-------|
| ERROR_CODE | `'TOO_LONG'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$message` | **string** |  |
| **protected** | `$errorCode` | **string** |  |




---

### ORM\Dbal\Type


**Implements:** [ORM\Dbal\TypeInterface](#ormdbaltypeinterface)

#### Base class for data types








#### Methods

* [factory](#ormdbaltypefactory) Returns a new Type object
* [fits](#ormdbaltypefits) Check if this type fits to $columnDefinition

#### ORM\Dbal\Type::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |





---

### ORM\Dbal\TypeInterface



#### Interface TypeInterface








#### Methods

* [factory](#ormdbaltypeinterfacefactory) Create Type class for given $dbal and $columnDefinition
* [fits](#ormdbaltypeinterfacefits) Check if this type fits to $columnDefinition
* [validate](#ormdbaltypeinterfacevalidate) Check if $value is valid for this type

#### ORM\Dbal\TypeInterface::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): \ORM\Dbal\Type
```

##### Create Type class for given $dbal and $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **\ORM\Dbal\Type**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\TypeInterface::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\TypeInterface::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

### ORM\Exception\UndefinedRelation

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exception\UnknownColumn

**Extends:** [ORM\Exception](#ormexception)


#### Base exception for ORM

Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Exception\UnsupportedDriver

**Extends:** [ORM\Exception](#ormexception)



Every ORM exception extends this class. So you can easily catch all exceptions from ORM.







---

### ORM\Event\Updated

**Extends:** [ORM\Event\UpdateEvent](#ormeventupdateevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'updated'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$dirty` | **array** |  |
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Event\UpdateEvent

**Extends:** [ORM\Event](#ormevent)








#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |
| **protected** | `$dirty` | **array** |  |



#### Methods

* [__construct](#ormeventupdateevent__construct) 
* [__get](#ormeventupdateevent__get) 

#### ORM\Event\UpdateEvent::__construct

```php
public function __construct( \ORM\Entity $entity, array $dirty ): UpdateEvent
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$entity` | **\ORM\Entity**  |  |
| `$dirty` | **array**  |  |



#### ORM\Event\UpdateEvent::__get

```php
public function __get( $name )
```




**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` |   |  |





---

### ORM\Event\Updating

**Extends:** [ORM\Event\UpdateEvent](#ormeventupdateevent)







#### Constants

| Name | Value |
|------|-------|
| NAME | `'updating'` |


#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$dirty` | **array** |  |
| **protected** | `$entity` | ** \ ORM \ Entity** |  |
| **protected** | `$data` | **array** |  |




---

### ORM\Dbal\Type\VarChar

**Extends:** [ORM\Dbal\Type](#ormdbaltype)


#### String data type

With and without max / fixed length




#### Properties

| Visibility | Name | Type | Description                           |
|------------|------|------|---------------------------------------|
| **protected** | `$maxLength` | **integer** |  |
| **protected** | `$type` | **string** |  |



#### Methods

* [__construct](#ormdbaltypevarchar__construct) VarChar constructor
* [factory](#ormdbaltypevarcharfactory) Returns a new Type object
* [fits](#ormdbaltypevarcharfits) Check if this type fits to $columnDefinition
* [getMaxLength](#ormdbaltypevarchargetmaxlength) 
* [validate](#ormdbaltypevarcharvalidate) Check if $value is valid for this type

#### ORM\Dbal\Type\VarChar::__construct

```php
public function __construct( integer $maxLength = null ): VarChar
```

##### VarChar constructor



**Visibility:** this method is **public**.
<br />


##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$maxLength` | **integer**  |  |



#### ORM\Dbal\Type\VarChar::factory

```php
public static function factory(
    \ORM\Dbal\Dbal $dbal, array $columnDefinition
): static
```

##### Returns a new Type object

This method is only for types covered by mapping. Use fromDefinition instead for custom types.

**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **static**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$dbal` | **\ORM\Dbal\Dbal**  |  |
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\VarChar::fits

```php
public static function fits( array $columnDefinition ): boolean
```

##### Check if this type fits to $columnDefinition



**Static:** this method is **static**.
<br />**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$columnDefinition` | **array**  |  |



#### ORM\Dbal\Type\VarChar::getMaxLength

```php
public function getMaxLength(): integer
```




**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **integer**
<br />



#### ORM\Dbal\Type\VarChar::validate

```php
public function validate( $value ): boolean|\ORM\Dbal\Error
```

##### Check if $value is valid for this type



**Visibility:** this method is **public**.
<br />
 **Returns**: this method returns **boolean|\ORM\Dbal\Error**
<br />

##### Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `$value` | **mixed**  |  |





---

