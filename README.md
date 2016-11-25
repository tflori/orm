# tflori/orm

**TL;DR** Others suck, we can do it better.

Why to create another ORM? There are not enough ORM implementations for PHP already?

Yes, there are a lot of implementations:
- doctrine/orm
  - heavy: 8,8 MB of everything that you don't need, 6 direct dependencies with own dependencies
  - annotations that makes it unreadable
  - big amount of queries or very slow queries when it comes to join across multiple tables
- propel/propel 
  - still not stable 2.0-dev 
  - even more heavy than doctrine
  - requires a lot of configurations
- j4mie/idiorim and j4mie/paris
  - uses a lot of static methods and gets hard to test
  - not compatible to existing dependecy injection models
  - last update 2 years ago
  - everything in one file
  - ...
  
This implementation will have the following features:
- no configuration required
  - ok some bit for sure (e.g. how to connect to your database?)
  - of course this is only possible if you setup your database as we think your database should look like. If not you
    should only have to setup the rules of your system and naming conventions.
- simple to use, lightweight sources, fast

How to achieve this features? The main goal of Doctrine seems to abstract everything - at the end you should be able
to replace the whole DBMS behind your app and switch from postgresql to sqlite. That requires not only a lot of
sources. It also requires some extra cycles to get these abstraction to work.
 
This library will only produce ANSI-SQL that every SQL database should understand. Other queries have to be written by
hand. This has two reasons:

1. You can write much faster and efficient queries
2. We don't need to write a lot of abstraction (more code; more bugs)

## Setup

Install it via composer, configure it, use it.
```bash
composer require tflori/orm
```

```php
<?php

DI::set('entityManager', function () {
    return new \ORM\EntityManager([
      \ORM\EntityManager::OPT_DEFAULT_CONNECTION => ['pgsql', 'mydb', 'postgres']
    ]);
});
```

Read [the docs](https://tflori.github.io/orm) for more information.
