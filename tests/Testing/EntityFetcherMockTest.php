<?php

namespace ORM\Test\Testing;

use Mockery as m;
use ORM\Exception;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;
use ORM\Testing\EntityFetcherMock;
use ORM\Testing\EntityFetcherMock\Result;
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

    protected function expectQuery($query, $rowCount)
    {
        $statement = m::mock(\PDOStatement::class);
        $statement->shouldReceive('rowCount')->andReturn($rowCount);
        $this->em->getConnection()->shouldReceive('query')->with($query)->once()->andReturn($statement);
    }

    /** @test */
    public function deleteReturnsCountFromMatchedResultWhenExpected()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)->expectDelete()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->delete();

        self::assertSame(2, $count);
    }

    /** @test */
    public function deleteExecutesTheStatementWhenNotExpected()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']));
        $this->expectQuery('DELETE FROM "article" AS t0', 3);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(3, $fetcher->delete());
    }

    /** @test */
    public function deleteExecutesTheStatementWhenNoResultMatches()
    {
        $this->expectQuery('DELETE FROM "article" AS t0', 3);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(3, $fetcher->delete());
    }

    /** @test */
    public function updateReturnsCountFromMatchedResultWhenExpected()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)->expectUpdate()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->update(['title' => 'Updated']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function updateExecutesTheStatementWhenNotExpected()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']));
        $this->expectQuery('UPDATE "article" AS t0 SET "title" = \'Updated\'', 3);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(3, $fetcher->update(['title' => 'Updated']));
    }

    /** @test */
    public function updateExecutesTheStatementWhenNoResultMatches()
    {
        $this->expectQuery('UPDATE "article" AS t0 SET "title" = \'Updated\'', 3);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(3, $fetcher->update(['title' => 'Updated']));
    }

    /** @test */
    public function insertReturnsRowCountWhenExpected()
    {
        $this->em->addResult(Article::class)->expectInsert()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $count = $fetcher->insert(['title' => 'Bar'], ['title' => 'Baz']);

        self::assertSame(2, $count);
    }

    /** @test */
    public function insertExecutesTheStatementWhenNotExpected()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']));
        $this->expectQuery('INSERT INTO "article" ("title") VALUES (\'Bar\')', 1);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(1, $fetcher->insert(['title' => 'Bar']));
    }

    /** @test */
    public function insertExecutesTheStatementWhenNoResultMatches()
    {
        $this->expectQuery('INSERT INTO "article" ("title") VALUES (\'Bar\')', 1);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(1, $fetcher->insert(['title' => 'Bar']));
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
    public function expectationsDefaultToOnce()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectDelete();

        self::expectException(m\Exception\InvalidCountException::class);
        $this->mockeryAssertPostConditions();
    }

    /** @test */
    public function theDefaultCountCanBeChanged()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->em->addResult(Article::class, ...$entities);
        $result->expectOne()->twice();
        $result->expectCount()->atLeast()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $articles = [$fetcher->one(), $fetcher->one()];
        $counts = [$fetcher->count(), $fetcher->count()];

        self::assertSame($entities, $articles);
        self::assertSame([2, 2], $counts);
    }

    /** @test */
    public function expectUpdateAcceptsMockeryMatchers()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectUpdate(m::subset(['status' => 'deleted']));

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(1, $fetcher->update(['status' => 'deleted', 'deletedAt' => '2026-10-15']));
    }

    /** @test */
    public function expectInsertAcceptsMockeryMatchers()
    {
        $this->em->addResult(Article::class)
            ->expectInsert(m::hasKey('title'), m::hasKey('title'));

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(2, $fetcher->insert(['title' => 'Bar'], ['title' => 'Baz']));
    }

    /** @test */
    public function expectationsCallTheRealMethodByDefault()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->em->addResult(Article::class, ...$entities);
        $result->expectDelete();
        $result->expectInsert();

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(2, $fetcher->delete());
        self::assertSame(3, $fetcher->insert(['title' => 'A'], ['title' => 'B'], ['title' => 'C']));
    }

    public function provideWriteMethods()
    {
        return [
            'delete' => ['expectDelete', 'delete', []],
            'update' => ['expectUpdate', 'update', [['title' => 'Updated']]],
            'insert' => ['expectInsert', 'insert', [['title' => 'A'], ['title' => 'B']]],
        ];
    }

    /** @dataProvider provideWriteMethods
     * @test */
    public function andReturnOverridesTheDefault($expectMethod, $method, $args)
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->$expectMethod()->andReturn(0);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(0, $fetcher->$method(...$args));
    }

    /** @dataProvider provideWriteMethods
     * @test */
    public function andReturnUsingOverridesTheDefault($expectMethod, $method, $args)
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->$expectMethod()->andReturnUsing(function () {
                return 42;
            });

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame(42, $fetcher->$method(...$args));
    }

    /** @dataProvider provideWriteMethods
     * @test */
    public function andThrowOverridesTheDefault($expectMethod, $method, $args)
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->$expectMethod()->andThrow(new \PDOException('Deadlock'));

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::expectException(\PDOException::class);
        self::expectExceptionMessage('Deadlock');

        $fetcher->$method(...$args);
    }

    /** @test */
    public function andReturnOverridesTheEntitiesOfOne()
    {
        $article = new Article(['title' => 'Bar']);
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))
            ->expectOne()->andReturn($article);

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame($article, $fetcher->one());
    }

    /** @test */
    public function expectationsRequireAMockedResult()
    {
        $result = new Result($this->em, Article::class);

        self::expectException(Exception::class);
        self::expectExceptionMessage('Expectations require a mocked result');

        $result->expectOne();
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
        $result->expectOne()->once();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $article = $fetcher->one();
        $articles = $fetcher->all();

        self::assertSame($entities[0], $article);
        self::assertSame([$entities[1]], $articles);
    }

    /** @test */
    public function allDoesNotCountAsCallsOfOne()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)
            ->expectOne()->never();

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $articles = $fetcher->all();

        self::assertSame($entities, $articles);
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

    /** @test */
    public function getResultsReturnsTheEntitiesOfTheMatchedResult()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities)->where('title', 'Foo');

        $fetcher = (new EntityFetcherMock($this->em, Article::class))->where('title', 'Foo');

        self::assertSame($entities, $this->em->getResults(Article::class, $fetcher));
    }

    /** @test */
    public function getResultsReturnsAnEmptyArrayWithoutMatch()
    {
        $this->em->addResult(Article::class, new Article(['title' => 'Foo']))->where('title', 'Foo');

        $fetcher = new EntityFetcherMock($this->em, Article::class);

        self::assertSame([], $this->em->getResults(Article::class, $fetcher));
    }

    /** @test */
    public function everyFetcherHasItsOwnCursor()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $fetcher1 = new EntityFetcherMock($this->em, Article::class);
        $fetcher1->all();

        $fetcher2 = new EntityFetcherMock($this->em, Article::class);
        $articles = $fetcher2->all();

        self::assertSame($entities, $articles);
    }

    /** @test */
    public function interleavedFetchersDoNotResetEachOther()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $articles = [];
        // limited to prevent an endless loop when the cursor gets reset
        for ($i = 0; $i < 5 && $article = $fetcher->one(); $i++) {
            $articles[] = $article;
            (new EntityFetcherMock($this->em, Article::class))->one();
        }

        self::assertSame($entities, $articles);
    }

    /** @test */
    public function resetStartsFromTheFirstEntity()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $this->em->addResult(Article::class, ...$entities);

        $fetcher = new EntityFetcherMock($this->em, Article::class);
        $fetcher->all();
        $fetcher->reset();

        self::assertSame($entities, $fetcher->all());
    }

    /** @test */
    public function allEagerLoadsRelationsFromWith()
    {
        $user = new User(['id' => 23]);
        $article = new Article(['id' => 42, 'user_id' => 23]);
        $this->em->addResult(User::class, $user);
        $this->em->addResult(Article::class, $article);

        $articles = $this->em->fetch(Article::class)->with('writer')->all();

        self::assertSame([$article], $articles);
        self::assertTrue($article->hasLoaded('writer'));
        self::assertSame($user, $article->getRelated('writer'));
    }

    /** @test */
    public function theFirstAddedResultWinsOnEqualScores()
    {
        $first = new Article(['title' => 'Foo']);
        $this->em->addResult(Article::class, $first)->where('title', 'Foo');
        $this->em->addResult(Article::class, new Article(['title' => 'Bar']))->where('title', 'Foo');

        $article = $this->em->fetch(Article::class)->where('title', 'Foo')->one();

        self::assertSame($first, $article);
    }
}
