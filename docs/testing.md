---
layout: default
title: Testing
permalink: /testing.html
---
## Testing

For development of this library we using Mockery and we suggest to use it for your test environment too.

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

```php?start_inline=true
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

```php?start_inline=true
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
        $em = $this->mocks['em'] = $this->ormInitMock([], 'mysql');
        
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

```php?start_inline=true
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
        $entity = $this->ormCreateMockEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
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

```php?start_inline=true
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
        $this->ormExpectInsert(Article::class, ['created' => date('c')]);
        
        createArticle('Anything');
    }
    
    // to proof that the result is correct you may need to overwrite the id too (otherwise it is random)
    public function testReturnsJsonEncodedArticle()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->ormExpectInsert(Article::class, $defaults);
        
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

```php?start_inline=true
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
        $this->ormExpectFetch(Article::class);
        
        getArticles();
    }
    
    // expect where condition
    public function testSearchesForTitle()
    {
        $fetcher = $this->ormExpectFetch(Article::class);
        $fetcher->shouldReceive('where')->with('title', 'LIKE', '%cambozola%');
        
        getArticles('cambozola');
    }
    
    // expect result (attention: the search is ignored)
    public function testReturnsArticles()
    {
        $articles = [new Article(), new Article()];
        $articles[0]->title = 'Hello World!';
        $articles[1]->title = 'Anything';
        $this->ormExpectFetch(Article::class, $articles);
        
        $result = getArticles('cambozola');
        
        self::assertSame($articles, $result);
    }
}
```

#### Updating an Entity

There is no need for complicated stuff:

1. [Create a mock](#createapartialmockentity)
2. Expect save method and maybe return the entity

But it might be neccessary that the entity is not dirty afterwards, to emulate the data has changed in the database
and the entity got new data while updated (triggers like `ON UPDATE CURRENT TIMESTAMP`). To make the save method act
like the original method act (without using the database) there is a helper method for it. 

```php?start_inline=true
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
        $this->ormExpectUpdate($article, ['changed' => date('c')]);
        
        updateArticle(42, 'Don`t Panic!');
    }
}
```

#### Deleting an Entity

When you deleting an Entity you will have to know which entity will come or at least from which class. Then you just
expect delete with `$em->shouldReceive('delete')->with($entity)->once()->andReturn(true)`. The helper for it just
removes the the original data like the delete method generally does.

```php?start_inline=true
function deleteArticle($id)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $id);
    $em->delete($article);
}

class ArticleControllerTest extends TestCase
{
    public function testDeleteWithoutHelper()
    {
        $article = $this->omCreateMockedEntity(Article::class, ['id' => 42]);
        
        $this->mocks['em']->shouldReceive('delete')->with($article)->once()->andReturn(true);
        
        deleteArticle(42);
    }
    
    public function testDeleteWithHelper()
    {
        $article = $this->omCreateMockedEntity(Article::class, ['id' => 42]);
        
        $this->ormExpectDelete($article);
        
        deleteArticle(42);
    }
}
```

### Mocking Relations

Relations are always taken from `$entity->fetch($relation[, true])`. You can easily mock this method with expecting 
the relation name as first and `true` as second parameter. It has to return an array or a single relation appropriate
to the definition of the relation. If you expecting fetch without the second parameter you can use the `ormExpectFetch`
helper.

```php?start_inline=true
function getArticleCategories($articleId)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $id);
    return $article->categories;    
}

function getFirstArticleCategory($articleId)
{
    $em = $GLOBALS['di']->get('entityManager');
    $article = $em->fetch(Article::class, $id);
    $fetcher = $article->fetch('categories');
    return $fetcher->one();
}

class ArticleControllerTest extends TestCase
{
    public function testFetchWithGetAll()
    {
        $categories = [new Category(), new Category()];
        $article = $this->omCreateMockedEntity(Article::class, ['id' => 42]);
        $article->expect('fetch')->with('categories', true)->once()->andReturn($categories);
        
        $result = getArticleCategories(42);
        
        self::assertSame($categories, $result);
    }
    
    public function testFetchUsingFetcher()
    {
        $categories = [new Category(), new Category()];
        $article = $this->omCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectFetch(Category::class, $categories);
        
        $result = getFirstArticleCategory(42);
        
        self::assertSame($categories[0], $result);
    }
}
```
