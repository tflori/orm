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
        'dbConfig' => new \ORM\DbConfig('mysql', 'any_db', 'any_user', 'any_password')
    ];
});

$di->set('entityManager', function () use ($di) {
    return new \ORM\EntityManager([
        'connection' => $di->get('config')['dbConfig']
    ]);
});

// and what ever else
```

In the `setUp()` method for your test case you have to mock the access to the database:

```php
<?php

abstract class TestCase extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var \Mockery\Mock[] */
    protected $mocks;
    
    protected function setUp()
    {
        parent::setUp();
        
        // load your dependency injection here
        $di = $GLOBALS['di'];
        
        // create a pdo mock object
        $pdo = $this->mocks['pdo'] = \Mockery::mock(\PDO::class);
        $pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mysql')->byDefault();
        
        // create a partial mock for entity manager
        $em = $this->mocks['entityManager'] = \Mockery::mock(\ORM\EntityManager::class, [])->makePartial();
        $em->shouldReceive('getConnection')->andReturn($pdo)->byDefault();
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

### Mocking fetch

```php
<?php

class ArticleControllerTest extends TestCase
{
    protected $controller;
    
    protected function setUp()
    {
        parent::setUp();
            
        $this->controller = new ArticleController();
    }
    
    public function testFetchesArticles()
    {
        $fetcher = \Mockery::mock(\ORM\EntityFetcher::class)->makePartial();
        $this->mocks['em']->shouldReceive('fetch')->with(Article::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('getAll')->once()->andReturn([]);
        
        $this->controller->getList();
    }
    
    public function testFetchesArticleById()
    {
        // we assume parameters are always string (from request uri)
        $params = [ 'id' => '42' ];
        $this->mocks['em']->shouldReceive('fetch')->with(Article::class, (int)$params['id'])->andReturn(null);
        
        $this->controller->getOne($params);
    }
}
```
