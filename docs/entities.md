---
layout: default
title: Working With Entities
permalink: /entities.html
---
## Working with entities

One of the most important things for an ORM: how to fetch an object from database? Directly followed by: how to get
related objects? It should be easy what means it should not need much lines to get an entity by primary key or even by
different where conditions.

```php
/** @var \ORM\EntityManager $entityManager */
$entityManager = $diContainer->get('entityManager'); // or what ever

// Get user with primary key 1
$user = $entityManager->fetch(User::class, 1);

// Get phone number with primary key [42, 'business'] (account id and phone type)
$phoneNumber = $entityManager->fetch(AccountPhoneNumber::class, [42, 'business']);

// Get user by login credentials
$user = $entityManager->fetch(User::class)
                      ->where('username', $username)
                      ->where('password', $password)
                      ->one();

// Get all phone numbers by account (later we will see how this is done by relations)
$phoneNumbers = $entityManager->fetch(AccountPhoneNumber::class)
                              ->where('accountId', $accountId)
                              ->all();
```

These examples sounds fairly easy? Let's see in details what it means.

### Fetching by primary key

Of course this is the fastest way and is very easy. The fetch method has two parameters: the entity class that should
be fetched and the optional primary key. Because the primary key can be combined primary key it is possible to give an
array here. Because it can have only one result it directly gives `->one()`.

It is also the fastest way because it will return the object stored in the entity map if the entity got fetched
already. This means that fetching by query will always query the database.

Fetching by primary key is a shortcut for the first row:

```php
$entityManager->fetch(User::class)->where('id', 1)->one();
$entityManager->fetch(User::class, 1);
```

> Because fetching with EntityFetcher also stores the entity in the entity map, the second row will not execute a
> statement on the database.

### Fetching with query builder

The query builder is a feature to write sql statements with a fluent interface. You can define where conditions, joins,
group by clause, ordering and even parenthesis for where conditions. You can use this tool to fetch entities by creating
an `EntityFetcher` with `EntityManager::fetch()` and providing the entity class. You can then receive one or all
entities from the query.

In fact `EntityFetcher` extends the `QueryBuilder` and for more information about building queries please have a look at
the [QueryBuilder](querybuilder.md) documentation.

Example usages:

```php
// fetch a user by $email
$user = $entityManager->fetch(User::class)->where('email', $email)->one();
// get all articles from last 7 days (mysql)
$articles = $entityManager->fetch(Article::class)
    ->where('created', '>', $entityManager::raw('DATE_SUB(NOW(), INTERVAL 7 DAY)'))
    ->all();
```

Please note that it is not possible to remove the predefined modifier `DISTINCT` and the column selection `t0.*` (the
entity table is aliased `t0`). However it is possible to add additional columns (for example aggregates from joined
tables) but keep in mind that you then maybe also need to group by the primary key of `t0`. Also it will not be possible
to update the entity in the DB with these extra columns.

Example with aggregate column:
```php
$albums = $entityManager->fetch(Album::class)
    ->joinRelated('images')
    ->groupBy('t0.id')
    ->column('COUNT(images.id)', [], 'imageCount')
    ->all();
```

### Set and get columns

Every column is available by magic getter and using the column naming previously described in documentation about
entity definitions. All values are stored twice and you can check if a column got changed and reset the whole entity
or a part of the entity.

```php
$user = $entityManager->fetch(User::class, 1);
echo $user->email . PHP_EOL; // someone@example.com
var_dump($user->isDirty()); // false

$user->email = 'foobar@example.com';
var_dump($user->isDirty('email')); // true

$user->reset('email');
var_dump($user->isDirty()); // false
```

### Add custom getter and setter

Imagine a datetime field in database. In your entity you will have a string. But to work with it it would be more nice
to have a `DateTime` object. To achieve this (and many other things) you can define custom getters and setters. When
someone accesses the property `created` the magic getter will check if there is a method `getCreated()`<sup>*</sup>
and return the output of this method instead. The same with writing access: by modify the property `created` the
setter will check if there is a method `setCreated($value)`<sup>*</sup> and will call this instead.

The data is stored in protected property $data.
 
```php
class User extends ORM\Entity
{
    /** @var DateTime */
    protected $created;
    
    public function getCreated()
    {
        if (!$this->created && !empty($this->data[static::getColumnName('created')])) {
            $this->created = new DateTime($this->data[static::getColumnName('created')]);
        }
        return $this->created;
    }
    
    public function setCreated(DateTime $created)
    {
        $this->data[static::getColumnName('created')] = $created->format('Y-m-d H:i:s');
    }
}
```

**\*** The methods have to follow `OPT_NAMING_SCHEME_METHODS`. So if you use `set_some_var` you should set the naming
scheme to `snake_lower`.
