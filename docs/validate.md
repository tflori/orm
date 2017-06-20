---
layout: default
title: Validate Data
permalink: /validate.html
---
## Validate Data

You can validate values for attributes by executing `Article::validate('userId', 42)` or for a set of attributes by
executing `Article::validateArray(['attribute' => 'value'])`. The database abstraction layer will describe your table
if possible and verify the values against the column type.

### Describe Your Table

The validation is done through the class `Table`. It is basically an array of the columns with a mapping for column
names. For mysql, postgres and sqlite we already implemented functions to describe the table through querying the
database.

#### Defining Special Types

At least in postgres it is possible to define custom types. These types are not mapped to a specific type and use the
validation from `Type\Text` (it only validates that the value is string). If you require a specific validator you
can create and register this validator.

The `Type` has to implement `TypeInterface` and can be registered through `Column::registerType($type)`.

Example for the type `point`:

```php?start_inline=true
namespace App\Type;

use ORM\Dbal\TypeInterface;
use ORM\Dbal\Dbal;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error;

class Point implements TypeInterface
{
    public static function factory(Dbal $dbal, array $columnDefinition) {
        return new static();
    }
    
    public static function fits(array $columnDefinition) {
        return $columnDefinition['data_type'] === 'point';
    }
    
    public function validate($value) {
        if (!is_string($value)) {
            return new NoString(['type' => 'point']);
        } elseif (!preg_match('/\([\d.]+,[\d.]+)/', $value)) {
            return new Error(['value' => $value], 'NO_POINT', '\'%value%\' is not a valid point');
        }
        
        return true;
    }
}
```

#### Describe Your Table Manually

May be your entity get not correctly described from dbal or you don't want to write a own database abstraction layer
for your database type. In this cases you will need to describe your table manually by overwriting the `::describe()`
method.

Here is a small example for a user Table:

```php?start_inline=true
use ORM\Entity;
use ORM\EntityManager;
use ORM\Dbal\Table;
use ORM\Dbal\Column;
use ORM\Dbal\Type;

class User extends Entity {
    public static function describe() {
        $dbal = EntityManager::getInstance(static::class)->getDbal();
        return new Table([
            new Column($dbal, [
                'column_name' => 'id',
                'data_type' => 'int',
                'type' => Type\Number::class,
                'is_nullable' => false,
                'column_default' => 'incremented'
            ]),
            new Column($dbal, [
                'column_name' => 'username',
                'data_type' => 'varchar',
                'type' => Type\VarChar::class,
                'is_nullable' => false,
                'column_default' => null,
                'character_maximum_length' => 20
            ]),
            new Column($dbal, [
                'column_name' => 'password',
                'data_type' => 'char',
                'type' => Type\VarChar::class,
                'is_nullable' => false,
                'column_default' => null,
                'character_maximum_length' => 40
            ]),
            new Column($dbal, [
                'column_name' => 'active',
                'data_type' => 'boolean',
                'type' => Type\Boolean::class,
                'is_nullable' => false,
                'column_default' => 0
            ]),
        ]);
    }
}
```

### Enable Validator

You can enable the validator for every attribute (except attributes with specific setters) by calling 
`::enableValidator()`. For easier handling there is also a `::disableValidator()` and you can disable or enable with
the opposite function by passing a boolean: `::disableValidator(false)` will enable the validator and 
`enableValidator(false)` will disable the validator. You can also set the static property `$enableValidator` to true.

When the validator is enabled each call to the magic setter that is not handled by a setter will validate the value. If
an Error is returned it will throw an `ORM\Dbal\Error` exception. This is also executed for the fill method. The
Idea behind this is that you can just fill your entity from unvalidated data source (such as `$_POST` or `php://input`).

```php?start_inline=true
/** @var User $user **/
if (isset($_SESSION['user']) && $user = $_SESSION['user']) {
    Article::enableValidator();
    $article = new Article();
    $article->setRelated('writer', $user);
    $article->fill($_POST);
    $article->save();
}
```

Fill has an extra option `$checkMissing` - as the name suppose it checks that absent columns/attributes are nullable.
This could lead to an unexpected behaviour if you use it with the example above (user_id is not set in `$_POST`).
