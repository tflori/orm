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
    EntityManager::OPT_DEFAULT_CONNECTION => new ORM\DbConfig('pgsql', 'mydb', 'postgres')
]);
```

If you are using dependency injection you can pass a function that has to return a `PDO` instance.

```php?start_inline=true
$diContainer = $GLOBALS['DI']; // what the heck? You don't know how to get your dependency injection container? we too!

$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_DEFAULT_CONNECTION  => function () use ($diContainer) {
        return $diContainer::get('pdoInstance');
    }
]);
```

For people with multiple databases they have to setup named database connections. Remember that you need to tell every
entity that should not use `default` the database name. Have a look at [Entity definitions](Entity/Definitions.md).

```php?start_inline=true
$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTIONS => [
        'default'       => new ORM\DbConfig('pgsql', 'mydb', 'postgres'),
        'datawarehouse' => new ORM\DbConfig('mysql', 'mydb_stats', 'someone', 'password', 'dw.local')
    ]
]);
```

You can also use the getter method here and use the `connection` attribute to provide `default`. Or directly pass a PDO
instance.

```php?start_inline=true
$diContainer = $GLOBALS['DI'];

$entityManager = new ORM\EntityManager([
    'connection' => function () use($diContainer) {
        return $diContainer::get('db.main');
    },
    'connections' => [
        'datawarehouse' => $diContainer::get('db.datawarehouse')
    ]
]);
```

> We are just checking if the function `is_callable()`. When the function is not returning an instance of `PDO` we
> throw an `ORM\ExceptionsTest\NoConnection` exception.

### Naming schemes

There are three settings:

| Setting                | Option                      | Description                          | Default         |
|------------------------|-----------------------------|--------------------------------------|-----------------|
| `$namingSchemeTable`   | `OPT_NAMING_SCHEME_TABLE`   | The naming scheme for table names.   | `'snake_lower'` |
| `$namingSchemeColumn`  | `OPT_NAMING_SCHEME_COLUMN`  | The naming scheme for column names.  | `'snake_lower'` |
| `$namingSchemeMethods` | `OPT_NAMING_SCHEME_METHODS` | The naming scheme for class methods. | `'camelCase'`   |

All these settings are stored as public static property in `Entity` class. This also means that you can overwrite this
variable in any subclass. And you can change this at runtime. We do not prevent you from making errors. But we insist
that you not change these values at runtime. Instead we suggest you to set these settings when creating the
`EntityManager` as option.

The available naming schemes are: `snake_lower`, `SNAKE_UPPER`, `camelCase`, `StudlyCaps`, `Snake_Ucfirst`,
`snake_case`, `lower` and `UPPER`.
