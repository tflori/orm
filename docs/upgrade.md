---
layout: default
title: Upgrade Guide
permalink: /upgrade.html
---
## Upgrade Guide

What changed between the versions and what you might have to adapt in your code.

### 1.10 → 1.11

#### Changed behaviour

- **Relations are resolved per class** ([#91](https://github.com/tflori/orm/issues/91)). Relations assigned to
  `static::$relations` in a boot method are stored for the booted class only - before, they were written into the
  `$relations` property shared by all entities that don't declare it themselves. `static::$relations` no longer
  contains resolved `Relation` objects.
- **Subclasses get their own relation object** with the subclass as parent. A subclass has to be in the morph map of
  a [morphed relation](relationDefinition.html#relations-in-subclasses) to be used as parent.
- **The boot method has to call `parent::boot()`** - otherwise traits and the boot methods of parent classes are not
  booted, and relations from the boot method of the parent class are not inherited. A missing `parent::boot()`
  triggers an `E_USER_WARNING`.
- **Magic getter**: relations from `<relation>Relation()` methods and the boot method are available through
  `$entity->relation` (and `isset()`) without resolving them first.
- **Fetching an entity again and `EntityManager::sync()` merge the row from the database** into the entity: columns
  that are not changed locally get the value from the database, local changes are kept. Before, only the original
  data was replaced - columns that were not changed locally became dirty and `save()` wrote the old values back.
  Columns that are not in the row (e.g. extra columns of a previous fetch) are kept.
- **`save()` writes only changed columns** - before, all columns were written. Extra columns from a fetch (e.g.
  aggregates) are not written anymore, and without changes no `UPDATE` is executed. Database side effects can change:
  MySQL `ON UPDATE CURRENT_TIMESTAMP` columns are updated now (they were set explicitly before), and triggers on
  specific columns (`UPDATE OF`) only fire when these columns change. Tests that expect the exact `UPDATE` statement
  have to be adapted.
- Removed the protected method `Relation::checkBoundTo()` and the exception "Relation already used for …".
- **New methods on `Entity`** that may conflict with methods of your entities: `hasRelation()`, `defineRelation()`
  and `storeRelationsFromBoot()`.

#### Testing

- `mockery/mockery` `^1.6` is required.
- `delete()`, `update()` and `insert()` of a mocked fetcher are only handled by a result when it has an expectation
  for it - otherwise they are executed on the mocked connection.
- Every mocked fetcher has its own cursor, and `->with()` eager loads relations.
- `EntityFetcherMock::$currentResult` was removed, `getResults()` is internal and deprecated.
- `EntityFetcherMock\Result` works in memory: `one()`, `all()`, `count()`, `delete()`, `update()` and `insert()`
  of a result don't execute queries anymore.
- `ormAllowUpdate()` returns a `ORM\Testing\SaveExpectation` (a `Mockery\Expectation`) instead of a
  `Mockery\CompositeExpectation`.

#### Not recommended anymore

- Assigning relations to `static::$relations` in the boot method - use `defineRelation()` or the static property
  `$relations` (see [Relation Definition](relationDefinition.html)).
- `ormExpectFetch()` / `ormAllowFetch()` - use `ormAddResult()` with expectations (see [Testing](testing.html)).

#### AI assistants

- The usage guide for AI assistants moved from `CLAUDE.md` to `USAGE.md`. If your project points its AI assistant to
  `vendor/tflori/orm/CLAUDE.md`, point it to `vendor/tflori/orm/USAGE.md` instead.

#### New

- `Entity::defineRelation($name, $definition)` and `Entity::hasRelation($name)`.
- Expectations on results of `ormAddResult()`: `expectOne()`, `expectAll()`, `expectCount()`, `expectDelete()`,
  `expectUpdate()` and `expectInsert()`.
- `ormExpectUpdate()` returns the expectation, `->withChanges($changes)` verifies the changes written by `save()`.
- Serialized entities contain the original data - after `unserialize()` only the changed columns are written.

### 1.9 → 1.10

#### Changed behaviour

- **PHP 7.3 or newer is required** (before: PHP 5.6).
- New method `Entity::delete()`: entities that define their own `delete()` method have to be compatible with it (or
  renamed).
- Entities use `__serialize()` / `__unserialize()` (instead of the deprecated `Serializable` interface methods);
  serialized entities from older versions can still be unserialized.

#### New

- `Entity::delete()` and documented transaction methods (`EntityManager::beginTransaction()`, `commit()`,
  `rollback()`).

### 1.8 → 1.9

#### Changed behaviour

- **New methods on `Entity`** that may conflict with methods of your entities: `boot()`, `bootIfNotBooted()`,
  `getRelation()`, `load()`, `hasLoaded()`, `resetRelated()`, `exists()`, `getDirty()`, `hasPrimaryKey()`,
  `observeBy()` and `detachObserver()`.
- `EntityFetcher::count()` wraps the query (`SELECT COUNT(*) FROM (…)`) instead of modifying the select clause.
- `EntityManager::beginTransaction()` inside a running transaction creates a savepoint (simulated nested
  transactions).

#### New

- Boot method for entities and traits (`boot()` and `boot<TraitName>()`), [filters](filters.html),
  [morphed relations](relationDefinition.html#morphed-relation-1),
  [the parent-children relation](relationDefinition.html#the-parent-children-relation),
  [eager loading](relations.html), [events and observers](events.html), query builder methods for `insert()`,
  `update()` and `delete()`, and `EntityManager::query()`.
- Relations can be defined in `<relation>Relation()` methods.

### 1.7 → 1.8

#### Testing

- The mocked `EntityFetcher` uses results added with `ormAddResult()` - every fetch is allowed now.
  `ormExpectFetch()` and `ormAllowFetch()` are deprecated.

#### Deprecated

- The static option getters and setters of `Entity` (e.g. `Entity::setTableNameTemplate()`,
  `Entity::setNamingSchemeColumn()`) - use `EntityManager::setOption()` / `getOption()` instead.

#### New

- `Entity::toArray()`.
