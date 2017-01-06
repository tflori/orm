---
layout: default
title: Configuration
permalink: /configuration.html
---
## Configuration

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
$diContainer = $GLOBALS['DI']; // what the heck? You don't know how to get your dependency injection container? we too!

$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTION  => function () use ($diContainer) {
        return $diContainer::get('pdoInstance');
    }
]);
```

> We are just checking if the function `is_callable()`. When the function is not returning an instance of `PDO` we
> throw an `ORM\ExceptionsTest\NoConnection` exception.

#### PDO Attributes

You can define PDO attributes in DbConfig. We highly recommend two settings for mysql and we set them by default:

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
from mysql against postgres. Of course you can still overwrite these attributes.

### Table Names And Naming Schemes

There are four settings:

| Setting                | Option                      | Description                          | Default         |
|------------------------|-----------------------------|--------------------------------------|-----------------|
| `$tableNameTemplate`   | `OPT_TABLE_NAME_TEMPLATE`   | The template for table names.        | `'%short%'`     |
| `$namingSchemeTable`   | `OPT_NAMING_SCHEME_TABLE`   | The naming scheme for table names.   | `'snake_lower'` |
| `$namingSchemeColumn`  | `OPT_NAMING_SCHEME_COLUMN`  | The naming scheme for column names.  | `'snake_lower'` |
| `$namingSchemeMethods` | `OPT_NAMING_SCHEME_METHODS` | The naming scheme for class methods. | `'camelCase'`   |

All these settings are stored as protected static property in `Entity` class. You can access them with static setters
and getters. After using them with `getTableName`, `getColumnName` or magic getters and setters you can not change
these values anymore. We suggest you use the options for `EntityManager` instead.

The available naming schemes are: `snake_lower`, `SNAKE_UPPER`, `camelCase`, `StudlyCaps`, `Snake_Ucfirst`,
`snake_case`, `lower` and `UPPER`. You can read more about table name template in
[Entity Definition](entityDefinition.html#template).
