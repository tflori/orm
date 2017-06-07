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

```php
<?php

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

```php
<?php

class User extends \ORM\Entity {
    
}
```
