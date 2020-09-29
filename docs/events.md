---
layout: default
title: Entity Events and Observers
permalink: /events.html
---
## Entity Events and Observers

Entities can be observed using observers. An observer has to implement the `ORM\ObserverInterface` and can be registered
in your entity manager for any subclass of `ORM\Entity`.

### Events

The events implemented in this library are:

| Event | Gets fired ... | Cancels Execution |
|-------|----------------|:-----------------:|
| `fetched` | after an entity got created from database | no |
| `changed` | after an attribute of an entity got changed using `Entity::setAttribute` | no |
| `saving`  | when `Entity::save` got called before anything happens | <mark>yes</mark> |
| `saved`   | after an entity got saved (inserted or updated) | no |
| `inserting` | before an entity gets inserted into the database | <mark>yes</mark> |
| `inserted` | after an entity got inserted into the database (before saved) | no |
| `updating` | before an entity gets updated in the database | <mark>yes</mark> |
| `updated` | after an entity got updated in the database (before saved) | no |
| `deleting` | when `EntityManager::delete` got called before the entity gets deleted | <mark>yes</mark> |
| `deleted` | after an entity got deleted from database | no |

When the observer returns false for `saving`, `inserting`, `updating` or `deleting` events it will cancel the execution.

### Observe Entities

To observe entities you can either define Observers and attach them or use the `ORM\Observer\CallbackObserver`. To
attach an observer you call `$em->observe(Entity::class, $observer)` on the entity manager instance responsible for
the class you want to observe. Or you use the static method `<Entity>::observeBy()` on the entity class.

The observe-method automatically creates and returns a callback observer when you not provide an observer so that you
can easily chain the calls:

```php
$em->observe(User::class)
    ->on('saved', function () {
        // do something fancy
    })->on('deleted', function () { 
        // ...
    });
```

### Stop and Detach Observers

Observers are executed in the order they are registered. When one of the observers returns false the rest of the
observers are not executed. But returning false will also cancel the execution (see [Events](#events)). To just stop
the execution you can use `$event->stop()`.

You can detach an observer with `$em->detatch($observer)` what removes them from all entities or
`$em->detatch($observer, Entity::class)` (or `<Entity>::detatchObserver($observer)`) to remove them for a specific
entity.

### Custom Observer

To create a custom observer you can either implement `ORM\ObserverInterface` or extend the 
`ORM\Observer\AbstractObserver`. The abstract observer splits the handler method to individual event-methods what may
be easier for development:

```php
class AuditObserver extends ORM\Observer\AbstractObserver
{
    protected function inserted(ORM\Event\Inserted $event)
    {
        // log that $event->entity got inserted
    }
}
```

### Custom Events

You can define custom events and implement them in your entities.

```php
class CustomEvent extends ORM\Event
{
    const NAME = 'loadingRelation';

    protected $relation;

    public function __construct(Entity $entity, $relation)
    {
        parent::__construct($entity);
        $this->relation = $relation;
    }
}

class Entity extends ORM\Entity
{
    public function fetch($relation, $getAll = false) 
    {
        $this->entityManager->fire(new CustomEvent($this, $relation));
        return parent::fetch($relation, $getAll);
    }
}

Entity::observeBy()->on('loadingRelation', function () {
    //...
});
```

### Event Methods

This section describes the methods that get called during the entity livecycle. As they have to be declared in the
entity classes you can't define independent observers. Probably all these methods will be declared deprecated in future
versions (except the onInit method).

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

```php
class User extends ORM\Entity
{
   public function onInit($new) {}
}
```

#### On Change Event

The change event get three parameters: the `$var` (string) that got changed, the `$oldValue` (mixed) and the current 
`$value` (mixed).

```php
class User extends ORM\Entity
{
   public function onChange($var, $oldValue, $value) {}
}
```

#### Pre/Post Update/Persist Events

These events don't get any parameters. Because the pre update/persist events get called before the statement gets
created you can modify the data here. The post update/persist get called very late, the id is now available and the own
data is resynchronized. You can persist other entities that are related - there is no magic that is doing it for you.

```php
class User extends ORM\Entity
{
    public function prePersist() {}
    public function postPersist() {}
    public function preUpdate() {}
    public function postUpdate() {}
}
```
