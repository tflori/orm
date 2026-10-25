---
layout: default
title: Testing
permalink: /testing.html
---
## Testing

For development of this library we are using Mockery and we suggest to use it for your test environment too. The testing
helpers described here require `mockery/mockery` `^1.6`.

What needs to be tested is up to you but we recommend to mock all the database access including quoting and querying
table definitions.
 
For easier use we created a trait that helps you with emulating the behaviour of `Entity` and `EntityManager`. All
methods have the prefix `orm` to not interfere with your methods.

> If you are not using mockery and don't want to have this dependency you will have to create your own helpers. This
> guide will just give some hints how to mock it.

### Setup

For testing you need to be able to inject a mock object into your code. We assume a dependency injection available in
`$di` with `->get('service')` and `->set('service', 'getter')`.

The production bootstrap could look like this:

```php
$di = new DependencyInjector();

$di->set('config', function () {
    return [
        'dbConfig' => new DbConfig('mysql', 'any_db', 'any_user', 'any_password')
    ];
});

$di->set('entityManager', function () use ($di) {
    return new EntityManager([
        'connection' => $di->get('config')['dbConfig']
    ]);
});

// and what ever else
```

In the `setUp()` method for your test case you have to mock the access to the database:

```php
abstract class TestCase extends MockeryTestCase
{
    use \ORM\Testing\MocksEntityManager;
    
    /** @var \Mockery\Mock[] */
    protected $mocks;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // load your dependency injection here
        $di = $GLOBALS['di'];
        
        // create the mock for EntityManager
        $em = $this->mocks['em'] = $this->ormInitMock([], 'mysql');
        
        // inject the entity manager mock
        $di->set('entityManager', function () use ($em) {
            return $em;
        });
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->closeMockery();
        
        // cleanup your dependency injection
    }
}
```

`ormInitMock($options, $driver)` creates an `EntityManagerMock` with the given options (as for the `EntityManager`
constructor) and a mocked `PDO` connection for the given driver (`mysql`, `pgsql`, `sqlite`, ...). Queries that are not
handled by the helpers below go to this connection - you get it with `$em->getConnection()`. For example for
`$em->fetch(Article::class)->where('created', '<', '2024-01-01')->delete()`:

```php
$this->mocks['em']->getConnection()->shouldReceive('query')
    ->with('DELETE FROM "article" AS t0 WHERE "t0"."created" < \'2024-01-01\'')
    ->once()->andReturn(\Mockery::mock(\PDOStatement::class, ['rowCount' => 3]));
```

**Note:** The following methods (defined in `MocksEntityManager` trait) rely on the `EntityManager::getInstance()` to
retrieve the responsible `EntityManagerMock` for the given `$class`. If you have multiple database connections and 
`EntityManager` instances and you use a different method to get the responsible `EntityManager` you have to adapt the
`MocksEntityManager::ormGetEntityManagerInstance($class): EntityManagerMock` method.

### Overview

Helper methods of the `ORM\Testing\MocksEntityManager` trait:

| Method                                                   | Description                                                   |
|----------------------------------------------------------|---------------------------------------------------------------|
| `ormInitMock($options = [], $driver = 'mysql')`          | Create the `EntityManagerMock` with a mocked connection       |
| `ormCreateMockedEntity($class, $data = [])`              | Create a partial mock of an entity (available through fetch)  |
| `ormAddResult($class, Entity ...$entities)`              | Provide entities for fetchers of `$class` (returns a `Result`) |
| `ormExpectInsert($class, $defaultValues = [])`           | Expect `save()` of a new entity of `$class`                   |
| `ormExpectUpdate($entity, $changingData = [], $updatedData = [])` | Expect `save()` of an existing (mocked) entity - chain `->withChanges($changes)` to verify the written changes (since 1.11) |
| `ormExpectDelete($entity)`                               | Expect `delete()` of an entity (or class name)                |
| `ormAllowInsert()`, `ormAllowUpdate()`, `ormAllowDelete()` | Same as `ormExpect*()` but without expecting a call         |
| `ormGetEntityManagerInstance($class)`                    | The `EntityManagerMock` responsible for `$class`              |
| `ormExpectFetch()`, `ormAllowFetch()`                    | **deprecated** - see [Migrating from `ormExpectFetch()`](#migrating-from-ormexpectfetch) |

Methods of the `Result` returned by `ormAddResult()`:

| Method                                                   | Description                                                   |
|----------------------------------------------------------|---------------------------------------------------------------|
| `where()`, `orWhere()`, `whereIn()`, `join()`, `groupBy()`, `orderBy()`, `limit()`, `offset()`, ... | Conditions the query has to match (same as `EntityFetcher`) |
| `matches($regex)`                                        | The query has to match the regular expression                 |
| `expectOne()`, `expectAll()`, `expectCount()`            | Expect that a matching fetcher returns entities / counts (since 1.11) |
| `expectDelete()`, `expectUpdate($data = null)`, `expectInsert(...$rows)` | Expect a delete, update or insert statement of a matching fetcher (since 1.11) |

### Create a Partial Mock Entity

Due to the fact that the entity constructor is final and calls an internal method (`onInit()`) you can not just create
the mock with `Mockery::mock(Article::class, [['id' => 42, 'title' => 'Hello World!']])->makePartial()`. Also creating
a passive mock with `Mockery::mock(new Article(['id' => 42, 'title' => 'Hello world!']))` does not work because
the original object is wrapped but the magic getter not.

You have to initialize the object without using the original constructor, set the entity manager manually, set the
original data and reset the entity. We created a helper for this to make it easier.

```php
class ArticleControllerTest extends TestCase
{
    public function testMockEntity()
    {
        $entity = \Mockery::mock(Article::class)->makePartial();
        $entity->setEntityManager($this->mocks['em']);
        $entity->setOriginalData(['id' => 42, 'title' => 'Hello World!']);
        $entity->reset();
        
        // now you can use the entity as usual and write expectations
    }
    
    public function testMockEntityUsingHelper()
    {
        $entity = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
        // when onInit is required call it now: $entity->onInit(false);
        
        // now you can use the entity as usual and write expectations
    }
}
```

### Mocking Create, Read, Update and Delete (CRUD)

First we want to have a look on the basics- so lets have a look on CRUD-Operations.

#### Creating an Entity

When a function creates an `Entity` it is mostly filling it with data and later call `->save()` to it. We can provide a
factory that we then can overwrite in the tests and instead of returning an `Entity` we can return a partial mock for
this `Entity`. How ever this will result in a function that we create only for tests.

The other way is that we mock the requests the `Entity` will cause when you call `->save()`. This is some bit tricky
because save is also for updating and the entity calls more than just `EntityManager::insert(Entity)`. The save method
makes two calls on `EntityManager` for a new `Entity`:

1. `sync(Entity)` - the `Entity` does not exist but when the primary key is incomplete this throws anyway
2. `insert(Entity, boolean)` - when the `Entity` has a primary key then it gets inserted without using auto increment

For an easier use we provide a method to expect exactly this. You can use it as follows:

```php
// the function that creates the entity
function createArticle($title)
{
    $article = new Article();
    $article->title = $title;
    $article->save();
    return json_encode($article);
}

class ArticleControllerTest extends TestCase
{    
    public function testSavesTheArticle()
    {
        $this->ormExpectInsert(Article::class, ['created' => '2024-01-01T12:00:00+00:00']);
        
        createArticle('Anything');
    }
    
    // to proof that the result is correct you may need to overwrite the id too (otherwise it is random)
    public function testReturnsJsonEncodedArticle()
    {
        $defaults = ['id' => 42, 'created' => '2024-01-01T12:00:00+00:00'];
        $this->ormExpectInsert(Article::class, $defaults);
        
        $article = createArticle('Anything');
        
        self::assertSame(array_merge($defaults, ['title' => 'Anything']), json_decode($article, true));
    }
}
```

#### Reading Entities

Reading entities should be much easier. Basically you expect that `EntityManager::fetch()` is called. If you expect a
specific primary key you can just create the entity and provide it through `EntityManager::map(Entity)` or you just
expect fetch to be called with `$class` and `$primaryKey` and return `$entity`. If you just want the EntityManager
to return the corresponding entity you don't have to mock it at all. Every entity created with `ormCreateMockedEntity`
will be available through `$entityManager->fetch($class, $primaryKey)`. We think for fetching a specific entity by
primary key you don't need a helper method.

```php
// the function that reads one article
function getArticle($id)
{
    $article = $GLOBALS['di']->get('entityManager')->fetch(Article::class, $id);
    return $article;
}

class ArticleControllerTest extends TestCase
{    
    // expect a call
    public function testGetsTheArticle()
    {
        $this->mocks['em']->shouldReceive('fetch')->with(Article::class, 42)->once();
        
        getArticle(42);
    }
    
    // provide and use it (we don't need to mock this functionality)
    public function testGetsTheArticleFromMap()
    {
        $article = $this->ormCreateMockedEntity(Article::class, [
            'id' => 42,
            'title' => 'Hello World!',
            'created' => '2024-01-01T12:00:00+00:00'
        ]);
         
        $result = getArticle(42);
         
        self::assertSame($article, $result);
    }
}
```

The EntityFetcher gets mocked with an empty result automatically. You can provide results for a fetcher of an entity
with `ormAddResult($class, Entity ...$entities)`. This returns an `ORM\Testing\EntityFetcherMock\Result` and
can then be specified to meet the expected where conditions, joins, grouping and ordering. Also the limit and offset
can be specified and if necessary you can force the query to match your regular expressions with
`->matches('/regex/')`. Basically the `Result` is
an `EntityFetcher`. So you can use it like an `EntityFetcher` to specify the conditions.

Expecting something specific can be more hard overall we recommend to just provide a basic result in the `setUp` method
of your TestCase. A result without conditions will be used if no other result matches.

```php
function getArticles($search = '')
{
    $fetcher = $GLOBALS['di']->get('entityManager')->fetch(Article::class);
    if (!empty($search)) {
        $fetcher->where('title', 'LIKE', '%' . $search . '%');
    }
    return $fetcher->all();
}

class ArticleControllerTest extends TestCase
{   
    // expect fetch
    public function testFetchesArticles()
    {
        $this->mocks['em']->shouldReceive('fetch')->with(Article::class)->once()->passthru();
        
        getArticles();
    }
    
    // expect where condition
    public function testSearchesForTitle()
    {
        $articles = [new Article(['title' => 'Hello World!']), new Article(['title' => 'Anything'])];
        $this->ormAddResult(Article::class, ...$articles)
            ->where('title', 'LIKE', '%cambozola%');
        
        $result = getArticles('cambozola');
        
        self::assertSame($articles, $result);
    }
    
    // assert empty result
    public function testReturnsEmptyResult()
    {
        $articles = [new Article(['title' => 'Hello World!']), new Article(['title' => 'Anything'])];
        $this->ormAddResult(Article::class, ...$articles); // fallback for any other query
        $this->ormAddResult(Article::class)->where('title', 'LIKE', '%cambozola%');
        
        $result = getArticles('cambozola');
        
        self::assertEmpty($result);
    }
}
```

##### Expecting Calls on Results

The `Result` returned by `ormAddResult()` is a Mockery mock. Besides the conditions you can define expectations how a
matching fetcher gets used: `expectOne()`, `expectAll()`, `expectCount()`, `expectDelete()`, `expectUpdate()` and
`expectInsert()`. Define the conditions first - the `expect*()` methods return the Mockery expectation.

An expectation expects exactly one call by default. Use `->times(n)`, `->twice()`, `->never()`, `->atLeast()->once()`
etc. to change it. Without `->andReturn*()` the fetcher returns what the result contains (the entities or the count of
them). With `->andReturn()`, `->andReturnUsing()` or `->andThrow()` you can overwrite it.

```php
class ArticleControllerTest extends TestCase
{
    // expect that the articles are fetched once
    public function testFetchesArticlesOnce()
    {
        $articles = [new Article(['title' => 'Hello World!']), new Article(['title' => 'Anything'])];
        $this->ormAddResult(Article::class, ...$articles)
            ->where('title', 'LIKE', '%cambozola%')
            ->expectAll();

        $result = getArticles('cambozola');

        self::assertSame($articles, $result);
    }

    // expect that the articles are never counted
    public function testDoesNotCountArticles()
    {
        $this->ormAddResult(Article::class)->expectCount()->never();

        getArticles();
    }
}
```

The same works for update, delete and insert statements executed through a fetcher (e.g.
`$em->fetch(Article::class)->where('id', $ids)->delete()`). `expectUpdate()` and `expectInsert()` optionally check
the passed data - you can also pass Mockery matchers like `\Mockery::subset()`. Delete and update return the number of
entities in the result, insert returns the number of rows - unless you overwrite the return value.

```php
function archiveArticles(array $ids)
{
    return $GLOBALS['di']->get('entityManager')->fetch(Article::class)
        ->where('id', $ids)
        ->update(['status' => 'archived']);
}

class ArticleControllerTest extends TestCase
{
    public function testArchivesTheArticles()
    {
        $this->ormAddResult(Article::class, new Article(['id' => 1]), new Article(['id' => 2]))
            ->where('id', [1, 2])
            ->expectUpdate(['status' => 'archived']);

        $count = archiveArticles([1, 2]);

        self::assertSame(2, $count);
    }

    public function testPassesTheErrorFromTheDatabase()
    {
        $this->ormAddResult(Article::class)
            ->expectUpdate(\Mockery::subset(['status' => 'archived']))
            ->andThrow(new \PDOException('Lock wait timeout exceeded'));

        $this->expectException(\PDOException::class);

        archiveArticles([1, 2]);
    }
}
```

Some things to keep in mind:

- A fetcher uses only one result: the one with the most matching conditions (on equal scores the first added result).
  A result with a condition that is not part of the query is skipped - so if your query does not match, the
  fetcher uses another result (or none) and your expectation fails because it was never called.
- A failing expectation (not called, called too often or called with different data) is reported when the test
  finishes - not when the method is called.
- Without an expectation `delete()`, `update()` and `insert()` are executed on the mocked connection, so you can
  still expect the query on the PDO mock (`$em->getConnection()`, see [Setup](#setup)).
- Every fetcher has its own cursor - two fetchers matching the same result return all entities. Relations defined with
  `->with()` get eager loaded.

##### Migrating from `ormExpectFetch()`

`ormExpectFetch()` and `ormAllowFetch()` are deprecated. They mock the `EntityFetcher` itself and ignore the conditions
of the query. Use `ormAddResult()` and the expectations above instead:

```php
// before
$this->ormExpectFetch(Article::class, $articles);

// now
$this->ormAddResult(Article::class, ...$articles)->expectAll();
// and if the call to fetch() itself has to be expected:
$this->mocks['em']->shouldReceive('fetch')->with(Article::class)->once()->passthru();
```

#### Updating an Entity

There is no need for complicated stuff:

1. [Create a mock](#create-a-partial-mock-entity)
2. Expect save method and maybe return the entity

But it might be necessary that the entity is not dirty afterwards, to emulate the data has changed in the database
and the entity got new data while updated (triggers like `ON UPDATE CURRENT TIMESTAMP`). To make the save method act
like the original method act (without using the database) there is a helper method for it. 

```php
function updateArticle($id, $newTitle)
{
    $article = $GLOBALS['di']->get('entityManager')->fetch(Article::class, $id);
    $article->title = $newTitle;
    $article->save();
}

class ArticleControllerTest extends TestCase
{    
    public function testCallsSave()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
        $article->shouldReceive('save')->once();
        
        updateArticle(42, 'Don`t Panic!');
        
        self::assertSame('Don`t Panic!', $article->title);
        // ATTENTION! the entity is still dirty:
        self::assertTrue($article->isDirty());
    }
    
    public function testWithHelper()
    {
        $article = $this->ormCreateMockedEntity(Article::class, [
            'id' => 42,
            'title' => 'Hello World!',
            'changed' => date('c', strtotime('-1 Hour')) 
        ]);
        $this->ormExpectUpdate($article, ['changed' => '2024-01-01T12:00:00+00:00']);
        
        updateArticle(42, 'Don`t Panic!');
    }
}
```

`save()` writes only the changed columns. To verify them, chain `withChanges()` to `ormExpectUpdate()` (since 1.11).
An array expects exactly these changes, so a column that is not in the array must not be written. A Mockery matcher
gets all changes as array of attribute => new value. The changes are compared after `preUpdate()`, so changes from
`preUpdate()` are included.

```php
$article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!', 'text' => '...']);

// exactly these changes - text must not be written
$this->ormExpectUpdate($article)->withChanges(['title' => 'Don`t Panic!']);
// or: at least these changes
// $this->ormExpectUpdate($article)->withChanges(\Mockery::subset(['title' => 'Don`t Panic!']));

updateArticle(42, 'Don`t Panic!');
```

When the changes don't match, `save()` throws a `Mockery\Exception` that shows the expected and the actual changes.

#### Deleting an Entity

When you deleting an Entity you will have to know which entity will come or at least from which class. Then you just
expect delete with `$em->shouldReceive('delete')->with($entity)->once()->andReturn(true)`. The helper for it just
removes the original data like the delete method generally does.

```php
function deleteArticle($id)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $id);
    $article->delete();
}

class ArticleControllerTest extends TestCase
{    
    public function testDeleteWithoutHelper()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        
        $this->mocks['em']->shouldReceive('delete')->with($article)->once()->andReturn(true);
        
        deleteArticle(42);
    }
    
    public function testDeleteWithHelper()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        
        $this->ormExpectDelete($article);
        
        deleteArticle(42);
    }
}
```

### Mocking Relations

Relations are always taken from `$entity->fetch($relation[, true])`. You can easily mock this method with expecting 
the relation name as first and `true` as second parameter. It has to return an array or a single relation appropriate
to the definition of the relation. If you expecting fetch without the second parameter you can add the expected results
with `ormAddResult($class, ...$entities)`.

```php
function getArticleCategories($articleId)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $articleId);
    return $article->categories;    
}

function getFirstArticleCategory($articleId)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $articleId);
    $fetcher = $article->fetch('categories');
    return $fetcher->one();
}

class ArticleControllerTest extends TestCase
{    
    public function testFetchWithGetAll()
    {
        $categories = [new Category(), new Category()];
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $article->shouldReceive('fetch')->with('categories', true)->once()->andReturn($categories);
        
        $result = getArticleCategories(42);
        
        self::assertSame($categories, $result);
    }
    
    public function testFetchUsingFetcher()
    {
        $categories = [new Category(), new Category()];
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormAddResult(Category::class, ...$categories);
        
        $result = getFirstArticleCategory(42);
        
        self::assertSame($categories[0], $result);
    }
}
```
