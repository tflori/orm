---
layout: default
title: Testing
permalink: /testing.html
---
## Testing

For development of this library we using Mockery and we suggest to use it for your test environment too.

What needs to be tested is up to you but we recommend to mock all the database access including quoting and querying
table definitions.

### Setup

For testing you need to be able to inject a mock object into your code. We assume a dependency injection available in
`$di` with `->get('service')` and `->set('service', 'getter')`.

The production bootstrap could look like this:

```php
<?php

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
<?php

abstract class TestCase extends MockeryTestCase
{
    use \ORM\MockTrait;
    
    /** @var \Mockery\Mock[] */
    protected $mocks;
    
    protected function setUp()
    {
        parent::setUp();
        
        // load your dependency injection here
        $di = $GLOBALS['di'];
        
        // create the mock for EntityManager
        $em = $this->mocks['em'] = $this->mockEntityManager([], 'mysql');
        
        // inject the entity manager mock
        $di->set('entityManager', function () use ($em) {
            return $em;
        });
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->closeMockery();
        
        // cleanup your dependency injection
    }
}
```

### Create a Partial Mock Entity

Due to the fact that the entity constructor is final and calls an internal method (`onInit()`) you can not just create
the mock with `Mockery::mock(Article::class, [['id' => 42, 'title' => 'Hello World!']])->makePartial()`. Also creating
a passive mock with `Mockery::mock(new Article(['id' => 42, 'title' => 'Hello world!']))` does not work because
the orignal object is wrapped but the magic getter not.

You have to initialize the object without using the original constructor, set the entity manager manually, set the
original data and reset the entity. We created a helper for this to make it easier.

```php
<?php

class ArticleControllerTest extends TestCase
{
    public function testMockEntity()
    {
        $entity = \Mockery::mock(Article::class)->makePartial();
        $entity->setEntityManager($this->mocks['em']);
        $entity->setOriginalData(['id' => 42, 'title' => 'Hello World!']);
        $entity->reset;
        
        // now you can use the entity as usual and write expectations
    }
    
    public function testMockEntityUsingHelper()
    {
        $entity = $this->emCreateMockEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
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
makes two calls methods on `EntiyManager` for a new `Entiy`:

1. `sync(Entity)` - the `Entity` does not exist but when the primary key is incomplete this throws anyway
2. `insert(Entity, boolean)` - when the `Entity` has a primary key then it gets inserted without using auto increment

For an easier use we provide a method to expect exactly this. You can use it as follows:

```php
<?php

// the function that creates the entity
function createArticle($title)
{
    $article = new Article();
    $article->title = $title;
    $article->save();
    return json_encode($article);
}

class ArticleControllerTest extends TestCase // uses ORM\MockTrait
{
    public function testSavesTheArticle()
    {
        $this->emExpectInsert(Article::class, ['created' => date('c')]);
        
        createArticle('Anything');
    }
    
    // to proof that the result is correct you may need to overwrite the id too (otherwise it is random)
    public function testReturnsJsonEncodedArticle()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->emExpectInsert(Article::class, $defaults);
        
        $article = createArticle('Anything');
        
        self::assertSame(array_merge($defaults, ['title' => 'Anything']), json_decode($article, true));
    }
}
```

> If you use (for performance reason) `$em->insert($entity)` directly in your code you can of course write your own 
> expectation for it: `$em->shouldReceive('insert')->with(anInstanceOf(Article::class))`.

#### Reading Entities

Reading entities should be much easier. Basically you expect that `EntityManager::fetch()` is called. If you expect a
specific primary key you can just create the the entity and provide it through `EntityManager::map(Entity)` or you
just expect fetch to be called once with `$class` ans `$primaryKey` and return `$entity`. We think for fetching a
specific entity by primary key you don't need a helper method.

For mocking an `EntityFetcher` the procedure will be more complicated. Therefore we created a helper method that act
like a real `EntityFetcher` by default. This method returns the mock from `EntityFetcher` this way you can expect
specific calls.

```php
<?php

// the function that reads one article
function getArticle($id)
{
    $article = $GLOBALS['di']->get('entityManager')->fetch(Article::class, $id);
    return $article;
}

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
    // expect a call
    public function testGetsTheArticle()
    {
        $this->mocks['em']->shouldReceive('fetch')->with(Article::class, 42)->once();
        
        getArticle(42);
    }
    
    // provide and use it (we don't need to mock this functionality)
    public function testGetsTheArticleFromMap()
    {
         $article = new Article(['id' => 42, 'title' => 'Hello World!', 'created' => date('c')]);
         $this->mocks['em']->map($article);
         
         $result = getArticle(42);
         
         self::assertSame($article, $result);
    }
    
    // expect fetch
    public function testFetchesArticles()
    {
        $this->emExpectFetch(Article::class);
        
        getArticles();
    }
    
    // expect where condition
    public function testSearchesForTitle()
    {
        $fetcher = $this->emExpectFetch(Article::class);
        $fetcher->shouldReceive('where')->with('title', 'LIKE', '%cambozola%');
        
        getArticles('cambozola');
    }
    
    // expect result (attention: the search is ignored)
    public function testReturnsArticles()
    {
        $articles = [new Article(), new Article()];
        $articles[0]->title = 'Hello World!';
        $articles[1]->title = 'Anything';
        $this->emExpectFetch(Article::class, $articles);
        
        $result = getArticles('cambozola');
        
        self::assertSame($articles, $result);
    }
}
```
