<?php

namespace ORM\Test\MockTrait;

use Mockery as m;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;
use ORM\Testing\EntityFetcherMock;
use ORM\Testing\MocksEntityManager;

class EntityFetcherMockTest extends TestCase
{
    use MocksEntityManager;

    /** @var EntityManager|m\MockInterface */
    protected $em;

    protected function setUp()
    {
        $this->em = $this->ormInitMock();
    }

    /** @test */
    public function returnsStoredEntity()
    {
        EntityFetcherMock::addEntity($original = new Article(['id' => 23]));

        $article = EntityFetcherMock::retrieve(Article::class, ['id' => 23]);

        self::assertSame($original, $article);
    }

    /** @test */
    public function returnsStoredMockedEntity()
    {
        $original = m::mock(Article::class)->makePartial();
        $original->__construct(['id' => 23]);
        EntityFetcherMock::addEntity($original);

        $article = EntityFetcherMock::retrieve(Article::class, ['id' => 23]);

        self::assertSame($original, $article);
    }

    /** @test */
    public function returnsDefinedResults()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertSame($entities, $articles);
    }

    /** @test */
    public function returnsAnEmptyArrayIfNoResultsAreDefined()
    {
        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertEmpty($articles);
    }

    /** @test */
    public function returnsAnEmptyArrayIfNoResultsMatch()
    {
        EntityFetcherMock::addResult(Article::class, $this->em, new Article(['title' => 'Foo']))
            ->where('title', 'Foo');

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertEmpty($articles);
    }

    /** @test */
    public function returnsEntitiesWithoutConditions()
    {
        EntityFetcherMock::addResult(Article::class, $this->em, new Article(['title' => 'Baz']))
            ->where('title', 'Baz');
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertSame($entities, $articles);
    }

    /** @test */
    public function returnsEntitiesThatMatchTheConditions()
    {
        EntityFetcherMock::addResult(Article::class, $this->em, ...[
            new Article(['title' => 'Foo']),
            new Article(['title' => 'Bar']),
        ]);
        EntityFetcherMock::addResult(Article::class, $this->em, $baz = new Article(['title' => 'Baz']))
            ->where('title', 'Baz');

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->where('title', 'Baz');
        $articles = $query->all();

        self::assertSame([$baz], $articles);
    }

    /** @test */
    public function returnsTheFirstEntities()
    {
        $entities = array_map(function ($i) {
            return new Article(['title' => 'Article ' . $i]);
        }, range(1, 10));
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all(5);

        self::assertSame(array_slice($entities, 0, 5), $articles);
    }

    /** @test */
    public function returnsTheNextEntities()
    {
        $entities = array_map(function ($i) {
            return new Article(['title' => 'Article ' . $i]);
        }, range(1, 10));
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->all(5);
        $articles = $query->all(5);

        self::assertSame(array_slice($entities, 5, 5), $articles);
    }

    /** @test */
    public function returnsOneEntity()
    {
        $entities = array_map(function ($i) {
            return new Article(['title' => 'Article ' . $i]);
        }, range(1, 10));
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);

        self::assertSame($entities[0], $query->one());
        self::assertSame($entities[1], $query->one());
    }

    /** @test */
    public function returnsTheResultCount()
    {
        $entities = array_map(function ($i) {
            return new Article(['title' => 'Article ' . $i]);
        }, range(1, 10));
        EntityFetcherMock::addResult(Article::class, $this->em, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->all(5);
        $count = $query->count();

        self::assertSame(10, $count);
    }
}
