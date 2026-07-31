<?php

namespace ORM\Test\Testing;

use Mockery as m;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;
use ORM\Testing\EntityFetcherMock;
use ORM\Testing\EntityManagerMock;
use ORM\Testing\MocksEntityManager;

class EntityFetcherMockTest extends TestCase
{
    use MocksEntityManager;

    /** @var EntityManagerMock|m\MockInterface */
    protected $em;

    protected function setUp(): void
    {
        $this->em = $this->ormInitMock();
    }

    /** @test */
    public function returnsStoredEntity()
    {
        $this->em->addEntity($original = new Article(['id' => 23]));

        $article = $this->em->retrieve(Article::class, ['id' => 23]);

        self::assertSame($original, $article);
    }

    /** @test */
    public function returnsStoredMockedEntity()
    {
        $original = m::mock(Article::class)->makePartial();
        $original->__construct(['id' => 23]);
        $this->em->addEntity($original);

        $article = $this->em->retrieve(Article::class, ['id' => 23]);

        self::assertSame($original, $article);
    }

    /** @test */
    public function returnsDefinedResults()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

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
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->where('title', 'Foo');

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertEmpty($articles);
    }

    /** @test */
    public function returnsAnEmptyResultIfMatched()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']));
        $this->em->addResult(Article::class)->where('title', 'Bar');

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->where('title', 'Bar');
        $articles = $query->all();

        self::assertEmpty($articles);
    }

    /** @test */
    public function returnsEntitiesWithoutConditions()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Baz']))
            ->where('title', 'Baz');
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $articles = $query->all();

        self::assertSame($entities, $articles);
    }

    /** @test */
    public function returnsEntitiesThatMatchTheConditions()
    {
        $this->em->addResult(Article::class, ...[
            new Article(['title' => 'Foo']),
            new Article(['title' => 'Bar']),
        ]);
        $this->em->addResult(Article::class, $baz = new Article(['title' => 'Baz']))
            ->where('title', 'Baz');

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->where('title', 'Baz');
        $articles = $query->all();

        self::assertSame([$baz], $articles);
    }

    /** @test */
    public function findsMatchedWhereConditions()
    {
        $this->em->addResult(Article::class, $entity = new Article(['title' => 'Foo Bar']))
            ->where('title', 'LIKE', '%foo%');

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->where('deleted_at', 'IS', null)
            ->where('title', 'LIKE', '%foo%');

        self::assertSame($entity, $query->one());
    }

    /** @test */
    public function returnsTheFirstEntities()
    {
        $entities = array_map(function ($i) {
            return new Article(['title' => 'Article ' . $i]);
        }, range(1, 10));
        $this->em->addResult(Article::class, ...$entities);

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
        $this->em->addResult(Article::class, ...$entities);

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
        $this->em->addResult(Article::class, ...$entities);

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
        $this->em->addResult(Article::class, ...$entities);

        $query = new EntityFetcherMock($this->em, Article::class);
        $query->all(5);
        $count = $query->count();

        self::assertSame(10, $count);
    }

    /** @test */
    public function deleteReturnsCountFromMatchedResult()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->delete();

        self::assertSame(2, $count);
    }

    /** @test */
    public function deleteReturnsZeroWhenNoResult()
    {
        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(0, $fetcher->delete());
    }

    /** @test */
    public function updateReturnsCountFromMatchedResult()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->update(['title' => 'Updated']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function updateReturnsZeroWhenNoResult()
    {
        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(0, $fetcher->update(['title' => 'Updated']));
    }

    /** @test */
    public function insertReturnsRowCount()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']));

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->insert(['title' => 'Bar'], ['title' => 'Baz']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function insertReturnsZeroWhenNoResult()
    {
        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(0, $fetcher->insert(['title' => 'Bar']));
    }

    /** @test */
    public function expectOneCountsCall()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectOne()->once();

        self::expectException(m\Exception\InvalidCountException::class);
        $this->mockeryAssertPostConditions();
    }

    /** @test */
    public function expectOnceRestrictsCallCount()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectOne()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $article = $fetcher->one();

        self::assertInstanceOf(Article::class, $article);
    }

    /** @test */
    public function expectDeleteNeverPassesWhenDeleteNotCalled()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectDelete()->never();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $article = $fetcher->one();

        self::assertInstanceOf(Article::class, $article);
    }

    /** @test */
    public function expectUpdateValidatesUpdateData()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectUpdate(['status' => 'deleted'])->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->update(['status' => 'deleted']);

        self::assertSame(1, $count);
    }

    /** @test */
    public function expectUpdateWithoutDataCountsCall()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)
            ->expectUpdate()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->update(['anything' => 'value']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function expectInsertValidatesInsertData()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectInsert(['title' => 'Bar'], ['title' => 'Baz'])->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->insert(['title' => 'Bar'], ['title' => 'Baz']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function expectInsertWithoutDataCountsCall()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectInsert()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->insert(['title' => 'New'], ['title' => 'Also New']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function expectAllCountsCall()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)
            ->expectAll()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $articles = $fetcher->all();

        self::assertCount(2, $articles);
    }

    /** @test */
    public function expectAllAndExpectOneOnSameResult()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->em->addResult(Article::class, ...$entities);
        $result->expectAll()->once();
        $result->expectOne()->atLeast()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $articles = $fetcher->all();

        self::assertCount(2, $articles);
    }

    /** @test */
    public function expectCountCountsCall()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)
            ->expectCount()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->count();

        self::assertSame(2, $count);
    }
}
