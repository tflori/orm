---
layout: default
title: Working With Entities
permalink: /entities.html
---
## Working with entities

One of the most important things for an ORM: how to fetch an object from database? Directly followed by: how to get
related objects? It should be easy what means it should not need much lines to get an entity by primary key or even by
different where conditions.

```php?start_inline=true
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

```php?start_inline=true
$entityManager->fetch(User::class)->where('id', 1)->one();
$entityManager->fetch(User::class, 1);
```

> Because fetching with EntityFetcher also stores the entity in the entity map, the second row will not execute a
> statement on the database.

### Fetching with query builder

By calling `EntityManager::fetch()` and providing only the entity class you will receive an object from 
`EntityFetcher`. This class implements the `QueryBuilderInterface` and uses the `QueryBuilder` to delegate the method
calls. The difference between the query builder and the `EntityFetcher` is that the `QueryBuilder` will just return the
query while the `EntityFetcher` will specify the columns to fetch and provides functions to fetch one or more entities.

Long text short: query builder builds an query (`string QueryBuilder::getQuery()`) and entity fetcher fetches objects of 
entities (`Entity EntityFetcher::all()`).

The `EntityFetcher` does not fetch relations. If you need to fetch a lot of objects and relations consider using more
than one entity fetcher. See the documentation about relations for more details.

For information how to join, build where conditions and so on please have look in the [API Reference](reference.md).

### Set and get columns

Every column is available by magic getter and using the column naming previously described in documentation about
entity definitions. All values are stored twice and you can check if a column got changed and reset the whole entity
or a part of the entity.

```php?start_inline=true
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
 
```php?start_inline=true
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

### Events

This library aims not to have magic. So it is very strictly about events. Events are called at specific point and there
is no magic like `cascade={persist,delete}`. There is also no post flush as this library has no unit of work and calls
are executed immediately.

| Method        | Get called ...                           |
|---------------|------------------------------------------|
| `onInit`      | after the entity got created.            |
| `onChange`    | after something got changed in data.     |
| `preUpdate`   | before the update statement get created. |
| `postUpdate`  | after the update statement got executed. |
| `prePersist`  | before the insert statement get created. |
| `postPersist` | after the insert statement got executed. |

#### On Init Event

The init event get a boolean that says whether the entity is new or not. A new entity is an entity that get created
by you without data or even with data. If you got the data from database add the second parameter to the constructor.

```php?start_inline=true
class User extends ORM\Entity
{
   public function onInit($new) {}
}
```

#### On Change Event

The change event get three parameters: the `$var` (string) that got changed, the `$oldValue` (mixed) and the current 
`$value` (mixed).

```php?start_inline=true
class User extends ORM\Entity
{
   public function onChange($var, $oldValue, $value) {}
}
```

#### Pre/Post Update/Persist Events

These events don't get any parameters. Because the pre update/persist events get called before the statement gets
created you can modify the data here. The post update/persist get called very late, the id is now available and the own
data is resynchronized. You can persist other entities that are related - there is no magic that is doing it for you.

```php?start_inline=true
class User extends ORM\Entity
{
    public function prePersist() {}
    public function postPersist() {}
    public function preUpdate() {}
    public function postUpdate() {}
}
```
