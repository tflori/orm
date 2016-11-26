---
layout: default
title: Entity Definition
permalink: /entityDefinition.html
---
## Entity Definition

Nothing is required and everything should work out of the box. It is like using PDO alone.

```php?start_inline=true
use ORM\Entity;

class User extends Entity {}

$user = $entityManager->fetch(User::class, 1);

echo $user->username . ' (' . $user->id . '): ' . $user->name;
```

To make this example work you need to have a table `user` with columns `id`, `username` and `name`. And maybe that is
different in your system. In further description we show how to setup differnt table name, column names and column 
aliases.

### Table name

The easiest way to define the table name is by adding the property `$tableName`.

```php?start_inline=true
use ORM\Entity;

class User extends Entity
{
  protected static $tableName = 'my_users';
}
```

The problem with this solution is that you have to enter it every time and it is not configurable. So we can set up
a table name template or overwrite the `getTableName()` method.

#### Template

We configure the table name template as string in the abstract Entity class.

```php?start_inline=true
// only short class name (without namespace)
Entity::$tableNameTemplate = '%short%'; 
namespace App\Models { class User extends \ORM\Entity {} }
echo App\Models\User::getTableName(); // 'user'

// the second part of namespace plus %short% class name
Entity::$tableNameTemplate = '%namespace[1]%_%short%';
namespace App\Car\Model { class Weel extends \ORM\Entity {} }
echo App\Car\Model\Weel::getTableName(); // 'car_weel'

// the comple name of the class
Entity::$tableNameTemplate = '%name%'; 
namespace Foo\Bar { class CustomerAddress extends \ORM\Entity {} }
echo Foo\Bar\CustomerAddress::getTableName(); // 'foo_bar_customer_address'

// only the namespace from third till end
Entity::$tableNameTemplate = '%namespace[2*]%';
namespace App\Modules\Gangsters\Car { class Entity extends \ORM\Entity {} }
echo App\Modules\Gangsters\Car\Entity::getTableName(); // 'gansters_car'

// the last two of the name (useful for psr-0 autoloaded classes)
Entity::$tableNameTemplate = '%name[0]%_%name[-1]%';
class Module_Model_Entity_UserAddress extends \ORM\Entity {}
echo Module_Model_Entity_UserAddress::getTableName(); // 'module_user_address'
```

As you can see there are three placeholders `%name%`, `%short%` and `%namespace%`. Short name is exploded by `_` and `\`
but the namespace is exploded by `\` only (PSR-0). You can access specific parts of name and namespace by brackets and
the rest of the namespace with a `*` character. The placeholders are converted by your naming scheme. The default
naming scheme is `snake_lower` what means that your StudlyCaps class name `CustomerAddress` gets converted to
`customer_address`.

To make it configurable at initialisation of `EntityManager` there is a configuration for it too.

```php?start_inline=true
new ORM\EntityManager([
  ORM\EntityManager::OPT_TABLE_NAME_TEMPLATE => '%namespace[2*]%'
]);
```

The table name template comes from static public variable and you can overwrite this variable in each entity if you
need to.

```php?start_inline=true

namespace Foo\Bar;

use ORM\Entity;

Entity::$tableNameTemplate = '%namespace%_%short%';

class Baz Extends Entity {
  public static $tableNameTemplate = '%short%';
}

echo Baz::getTableName(); // 'baz'
```

> The tables names are stored in a protected static variable in Entity. It is not a good idea to change the template at
> runtime because you will get unexpected behaviour when the template got changed before or after you get the name of 
> the table the first time.

#### Overwrite getter

If you want totally different naming scheme do what ever you want:

```php?start_inline=true
namespace App\Model;

abstract class Entity extends \ORM\Entity {
    public static function getTableName() {
        return str_replace('\\', '_', static::class);
    }
}
```
