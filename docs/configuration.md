---
layout: default
title: Configuration
permalink: /configuration.html
---
## Configuration

### Database Configuration

A project without dependency injection, different databases, database cluster or anything else can just use configure
with the parameters from `DBConfig`. Or create a `DBConfig` and pass it.

```php
<?php

use ORM\EntityManager;

$entitymanager = new EntityManager([
    'connection' => ['pgsql', 'mydb', 'postgres']
]);

// suggested in favor of type hinting
$entitymanager = new EntityManager([
    EntityManager::OPT_DEFAULT_CONNECTION => new ORM\DBConfig('pgsql', 'mydb', 'postgres')
]);
```

If you are using dependency injection you can pass a function that has to return a `PDO` instance.

```php
<?php
$diContainer = $GLOBALS['DI']; // what the heck? You don't know how to get your dependency injection container? we too!

$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_DEFAULT_CONNECTION  => function () use ($diContainer) {
        return $diContainer::get('pdoInstance');
    }
]);
```

For people with multiple databases they have to setup named database connections. Remember that you need to tell every
entity that should not use `default` the database name. Have a look at [Entity definitions](Entity/Definitions.md).

```php
<?php

$entityManager = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTIONS => [
        'default'       => new ORM\DBConfig('pgsql', 'mydb', 'postgres'),
        'datawarehouse' => new ORM\DBConfig('mysql', 'mydb_stats', 'someone', 'password', 'dw.local')
    ]
]);
```

You can also use the getter method here and use the `connection` attribute to provide `default`. Or directly pass a PDO
instance.

```php
<?php
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
> throw an `ORM\Exceptions\NoPDOConnection` exception.
