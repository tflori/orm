---
layout: default
title: Entity Definition
permalink: /entityDefinition.html
---
## Entity Definition

Nothing is required and everything should work out of the box. It is like using `PDO` alone.

```php?start_inline=true
class User extends ORM\Entity {}

$user = $entityManager->fetch(User::class, 1);

echo $user->username . ' (' . $user->id . '): ' . $user->name;
```

To make this example work you need to have a table `user` with columns `id`, `username` and `name`. And maybe that is
different in your system. In further description we show how to setup different table name, column names, column 
aliases and identifier.

All table and column names get quoted in queries. The usual way for quoting in SQL is with double quote `"`.
Table names can also be in separated schemas or databases (in mysql) it is usually divided by a dot `.`. Maybe your
database is different (mysql uses `` ` `` for quoting) - then you can define them with the options 
`OPT_QUOTING_CHARACTER` and `OPT_IDENTIFIER_DIVIDER`.

> For mysql we suggest to use `PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode ='ANSI_QUOTES'"`

This orm library also handles relations - for more information about configuring relations check
[Relation Definition](relationDefinition.md).

### Table name

The easiest way to define the table name is by adding the property `$tableName`.

```php?start_inline=true
use ORM\Entity;

class User extends Entity
{
  protected static $tableName = 'my_users';
}
```

How ever if you have by general a different table name than class name but they are related you can configure a
different table name template with the `OPT_TABLE_NAME_TEMPLATE`. You can also overwrite the `getTableName()` static
method in an abstract class.

#### Template

The table name template can be configured in the `Namer` but you should better pass it to the options for the 
EntityManager during initialization:

```php?start_inline=true
// only short class name (without namespace; this is the default)
new ORM\EntityManager(['tableNameTemplate' => '%short%']);
namespace App\Models { class User extends \ORM\Entity {} }
echo App\Models\User::getTableName(); // 'user'

// the second part of namespace plus %short% class name
new ORM\EntityManager(['tableNameTemplate' => '%namespace[1]%_%short%']);
namespace App\Car\Model { class Weel extends \ORM\Entity {} }
echo App\Car\Model\Weel::getTableName(); // 'car_weel'

// the comple name of the class
new ORM\EntityManager(['tableNameTemplate' => '%name%']);
namespace Foo\Bar { class CustomerAddress extends \ORM\Entity {} }
echo Foo\Bar\CustomerAddress::getTableName(); // 'foo_bar_customer_address'

// only the namespace from third till end
new ORM\EntityManager(['tableNameTemplate' => '%namespace[2*]%']);
namespace App\Modules\Gangsters\Car { class Entity extends \ORM\Entity {} }
echo App\Modules\Gangsters\Car\Entity::getTableName(); // 'gangsters_car'

// the first and last part of the name (useful for psr-0 autoloaded classes)
new ORM\EntityManager(['tableNameTemplate' => '%name[0]%_%name[-1]%']);
class Module_Model_Entity_UserAddress extends \ORM\Entity {}
echo Module_Model_Entity_UserAddress::getTableName(); // 'module_user_address'
```

As you can see there are three placeholders `%name%`, `%short%` and `%namespace%`. Name is exploded by `_` and `\`
but the namespace is exploded by `\` only (PSR-0). You can access specific parts of name and namespace by brackets and
the rest of the namespace with a `*` character. The placeholders are converted by your naming scheme. The default
naming scheme is `snake_lower` what means that your StudlyCaps class name `CustomerAddress` gets converted to
`customer_address`.

#### Overwrite getter

The name of a table can be obtained by the public static method `getTableName()`. You can overwrite this function if you
need to or have a different logic to get your table name. 

```php?start_inline=true
namespace App\Model;

abstract class Entity extends \ORM\Entity {
    public static function getTableName() {
        return str_replace('\\', '_', static::class);
    }
}
```

### Column names and aliases

With setting up the table name everything is done. You can access your tables and on the entity instance you can access
every column with it's name through magic getter. If you want different attribute names in your class than column names
in your table you have to let the Entity know how your names should look like.

You might ask how to achieve code completion. There are different options how to get code completion. The easiest way
is to add `@property` annotations to your class. Another way would be to define constants for column names and use
them for `->__get(Article::COLUMN_ID)` and `->__set(Article::COLUMN_ID)`. And the most work you have by defining
getters and setters for each property. It's up to you.

Column names follow the naming scheme for columns defined for the EntityManager. You can also define a prefix per 
entity. When you use prefixes than the prefix have to be in the correct naming scheme for columns.

```php?start_inline=true
/**
 * @property int id
 * @property string username
 */
class User extends ORM\Entity 
{
    protected static $columnPrefix = 'usr_';
}

User::getColumnName('id'); // usr_id
User::getColumnName('username'); // usr_username

// CAUTION!
User::getColumnName('usr_username'); // usr_username but
User::getColumnName('usrUsername'); // usr_usr_username
```

You can also provide column aliases:
 
```php?start_inline=true
/**
 * @property foo
 * @property gender
 */
class User extends ORM\ENtity
{
    protected static $columnAliases = [
        'foo' => 'bar',
        'gender' => 'sex'
    ];
}

User::getColumnName('foo'); // bar
User::getColumnName('gender'); // sex
```

#### Overwrite getter

The name of a column can be obtained by the public static method `getColumnName($name)`. You can overwrite this function
if you need to or have a logic for your column prefixing.

**IMPORTANT: `getColumnName(getColumnName($name))` should always return the same as `getColumnName($name)`.**

```php?start_inline=true
namespace App\Model;

abstract class Entity extends \ORM\Entity {
    public static function getColumnName($name) {
        return self::forceNamingScheme($name, static::$namingSchemeColumn);
    }
}
```

### Identifier

Every table needs an identifier but the identifier can also be a combined primary key. The identifier is usually the 
`id` column and it is autoincrement. That is the default and you don't have to configure anything. Maybe your column
has different name and you just need to add an alias (see Column names and aliases). Or you set up your primary key
as protected static `$primaryKey`.

The identifier can also be non auto incremental (as it is automatically when it is a combined primary key). To define
a non auto incremental identifier you set the protected static property `$autoIncrement` to false. To save an entity the
primary key has to be filled.

```php?start_inline=true
// Naming scheme: snake_lower

class A extends ORM\Entity {
    protected static $primaryKey = 'aId'; // column name a_id
}

class B extends ORM\Entity { // table name b
    protected static $columnAliases = [
        'id' => 'bId'
    ];
}

class AB extends ORM\Entity {
    protected static $primaryKey = ['aId', 'bId']; // column names a_id b_id
}

A::getColumnName('id'); // id -> you have to access the id column via aId
B::getColumnName('id'); // b_id -> you can access the id column
```
