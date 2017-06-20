---
layout: default
title: Configuration
permalink: /configuration.html
---
## Configuration

The `ORM` gets configured through an array of options in the constructor of `EntityManager`. The keys of this array
should be `EntityManager::OPT_*` constants but you can also use the strings if you prefer (once the library is
stable the keys should only change when the meaning changes - so never).

The entities get defined through classes extending `Entity`. More about this you can read in
[Entity Definition](entityDefinition.md).

**Attention:** You have to initialize an `EntityManager` before accessing table or column names, creating new entities
or restoring serialized entities. This is a breaking change in version 1.2. This comes through internal dependencies
that are managed through `EntityManager`. It is save to initialize `EntityManager` as it only connects to database
when it is required.

### Database Configuration

A project without dependency injection, different databases, database cluster or anything else can just use configure
with the parameters from `DbConfig`. Or create a `DbConfig` and pass it.

```php?start_inline=true
use ORM\EntityManager;

$entitymanager = new EntityManager([
    'connection' => ['pgsql', 'mydb', 'postgres']
]);

// suggested in favor of type hinting
$entitymanager = new EntityManager([
    EntityManager::OPT_CONNECTION => new ORM\DbConfig('pgsql', 'mydb', 'postgres')
]);
```

If you are using dependency injection you can pass a function that has to return a `PDO` instance.

```php?start_inline=true
$diContainer = $GLOBALS['DI']; // sorry we don't know how you get your depenency injection container

$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTION  => function () use ($diContainer) {
        return $diContainer->get('pdoInstance');
    }
]);

$diContainer->set('entityManager', $entityManager);
```

> We are just checking if the function `is_callable()`. When the function is not returning an instance of `PDO` we
> throw an `ORM\ExceptionsTest\NoConnection` exception.

#### PDO Attributes

You can define PDO attributes in `DbConfig`. We highly recommend two settings for mysql and we set them by default:

```php?start_inline=true
[
    // Change sql_mode to ANSI_QUOTES and communication to utf8 on connect
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode ='ANSI_QUOTES', NAMES utf8",
    
    // Dont emulate prepare statements
    PDO::ATTR_EMULATE_PREPARES => false,
]
```

With `ANSI_QUOTES` we can use the default quoting character `"` for identifier (columns and tables) and by disabling
emulation of prepare statements we get integer and float from numeric columns. These are the most annoying drawbacks
from mysql against postgres. Of course you can still overwrite these attributes. Don't forget to change the quoting
character if you disable `ANSI_QUOTES` in mysql.

### Table Names And Naming Schemes

These settings are for global naming definition of tables, columns and methods. They should noch change during runtime.

| Option                | Const                       | Description                      | Default       |
|-----------------------|-----------------------------|----------------------------------|---------------|
| `tableNameTemplate`   | `OPT_TABLE_NAME_TEMPLATE`   | Template for table names.        | `%short%`     |
| `namingSchemeTable`   | `OPT_NAMING_SCHEME_TABLE`   | Naming scheme for table names.   | `snake_lower` |
| `namingSchemeColumn`  | `OPT_NAMING_SCHEME_COLUMN`  | Naming scheme for column names.  | `snake_lower` |
| `namingSchemeMethods` | `OPT_NAMING_SCHEME_METHODS` | Naming scheme for class methods. | `camelCase`   |

The available naming schemes are: `snake_lower`, `SNAKE_UPPER`, `camelCase`, `StudlyCaps`, `Snake_Ucfirst`,
`snake_case`, `lower` and `UPPER`. You can read more about table name template in
[Entity Definition](entityDefinition.md#template).


### Database abstraction layer options

The database abstraction layer has some options that you might want to change dependeing on the database you use.

| Option              | Const                    | Description                                        | Default |
|---------------------|--------------------------|----------------------------------------------------|---------|
| `dbalClass`         | `OPT_DBAL_CLASS`         | Overwrite the Class to use for DatabaseAbstraction | `null`  |
| `quotingChar`       | `OPT_QUOTING_CHARACTER`  | Character used for quoting tables and columns      | `"`     |
| `identifierDivider` | `OPT_IDENTIFIER_DIVIDER` | Character for dividing tables, columns and schemas | `.`     |
| `true`              | `OPT_BOOLEAN_TRUE`       | String how to pass boolean to database             | `1`     |
| `false`             | `OPT_BOOLEAN_FALSE`      | See OPT_BOOLEAN_TRUE                               | `0`     |

> For postgres (pgsql) the default for OPT_BOOLEAN_TRUE is `true` and for OPT_BOOLEAN_FALSE it is `false`.

> The dbal class will be determined by pdo driver. There are specific classes for Mysql, Pgsql and Sqlite - for others
> `describe()` and `insert()` with autoincrement will not work.

### Multiple EntityManagers and Databases

The `EntityManager` stores instances in a static variable to make internal dependency management. By default it uses the
last `EntityManager` that got initialized. When you have multiple databases you should define that one `EntityManager`
is only responsible for a specific set of classes and the other for everything else. Mostly you will not know what
is the last `EntityManager` that got created.

They also share the database abstraction layer and the naming service. Both of them have specific options that might
be different for some specific Entities even if they share the same database. Mostly this is due to legacy code like
"in the past we used `ENUM('Y','N')` for booleans and now we use `TINYINT`".

There are two options to specify the `EntityManager` to use for a class: `$em->defineForNamespace('Name\\Space')` and
`$em->defineForParent('OldEntity')`.

#### Define EntityManager for Parent Class

When you define an `EntityManager` to be responsible for a parent class it means that the class is being checked if it
is a subclass of this class. This is done due to `ReflectionClass::isSubclassOf($parent)`.

If you have legacy tables that you don't want to migrate you may want to create an own `EntityManager` for it:

```php?start_inline=true
use ORM\EntityManager;
use ORM\Entity;

abstract class OldEntity extends Entity {}
abstract class NewEntity extends Entity {}

class SomeThing extends OldEntity {}
class Another extends NewEntity {}

$em = new EntityManager([
    EntityManager::OPT_CONNECTION => 'getPdoConnection'
]);
$em->defineForParent(NewEntity::class);

$em = new EntityManager([
    EntityManager::OPT_CONNECTION => 'getPdoConnection',
    EntityManager::OPT_BOOLEAN_TRUE => '\'y\'',
    EntityManager::OPT_BOOLEAN_TRUE => '\'n\'',
]);
$em->defineForParent(OldEntity::class);
```

#### Define EntityManager for Namespace

You can also define an `EntityManager` to be responsible for a specific Namespace. This could be helpful for a library
that is a part of your application. The check if the class is from this Namespace we check if the full class name
begins with the namespace.

The above example uses the last `EntityManager` for other classes. You might want to make this example more save by 
defining the last `EntityManager` for the Namespace `App`. The parent matching has precedents so that the following
example will achieve the same:

```php?start_inline=true
namespace App;

use ORM\EntityManager;
use ORM\Entity;

abstract class OldEntity extends Entity {}

class SomeThing extends OldEntity {}
class Another extends Entity {}

$em = new EntityManager([
    EntityManager::OPT_CONNECTION => 'getPdoConnection'
]);
$em->defineForNamespace('App');

$em = new EntityManager([
    EntityManager::OPT_CONNECTION => 'getPdoConnection',
    EntityManager::OPT_BOOLEAN_TRUE => '\'y\'',
    EntityManager::OPT_BOOLEAN_TRUE => '\'n\'',
]);
$em->defineForParent(OldEntity::class);
```
