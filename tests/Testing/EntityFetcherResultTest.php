<?php

namespace ORM\Test\Testing;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;
use ORM\Testing\EntityFetcherMock\Result;
use ORM\Testing\MocksEntityManager;

class EntityFetcherResultTest extends TestCase
{
    use MocksEntityManager;

    /** @var EntityManager|m\MockInterface */
    protected $em;

    protected function setUp(): void
    {
        $this->em = $this->ormInitMock();
    }

    /** @test */
    public function returnsOneWithoutConditions()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        self::assertSame(1, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenWhereDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->where('col', '=', 'Foo');
        $result->where('col', '=', 'Bar');

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenJoinDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->join('category', 'categoryId = category.id');
        $result->join('user', 'creator = user.id');

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenGroupingDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->groupBy('category');
        $result->groupBy('creator');

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenOrderingDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->orderBy('created');
        $result->orderBy('creator');

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenCustomRegExDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $result->matches('/GROUP_CONCAT\(col\)/');

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenLimitDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->limit(20);
        $result->limit(10);

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsZeroWhenOffsetDoesNotMatch()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->limit(10)->offset(10);
        $result->limit(10)->offset(20);

        self::assertSame(0, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenOneWhereMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->where('col', '=', 'Foo');
        $result->where('col', '=', 'Foo');

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenJoinMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->join('user', 'creator = user.id');
        $result->join('user', 'creator = user.id');

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenGroupingMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->groupBy('creator');
        $result->groupBy('creator');

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenOrderingMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->orderBy('creator');
        $result->orderBy('creator');

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenCustomRegExMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->where('categoryId', [1,42,13]);
        $result->matches('/category_id" IN \((\d+,)*42/');

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenLimitMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        $query->limit(10);
        $result->limit(10);

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function returnsTwoWhenOffsetMatches()
    {
        $result = new Result($this->em, Article::class);
        $query = new EntityFetcher($this->em, Article::class);

        // offset is only relevant when limit is given
        $query->limit(10)->offset(10);
        $result->limit(10)->offset(10);

        self::assertSame(2, $result->compare($query));
    }

    /** @test */
    public function oneReturnsFirstEntity()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);

        self::assertSame($entities[0], $result->one());
    }

    /** @test */
    public function oneIteratesThroughEntities()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);

        $result->one();

        self::assertSame($entities[1], $result->one());
    }

    /** @test */
    public function oneReturnsNullWhenNoEntities()
    {
        $result = $this->ormAddResult(Article::class);

        self::assertNull($result->one());
    }

    /** @test */
    public function oneReturnsNullAfterAllEntities()
    {
        $entities = [new Article(['title' => 'Foo'])];
        $result = $this->ormAddResult(Article::class, ...$entities);
        $result->one();

        self::assertNull($result->one());
    }

    /** @test */
    public function countReturnsEntityCount()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);

        self::assertSame(2, $result->count());
    }

    /** @test */
    public function countReturnsZeroWhenNoEntities()
    {
        $result = $this->ormAddResult(Article::class);

        self::assertSame(0, $result->count());
    }

    /** @test */
    public function countIsUnaffectedByOneCalls()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);
        $result->one();

        self::assertSame(2, $result->count());
    }

    /** @test */
    public function deleteReturnsEntityCount()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);

        self::assertSame(2, $result->delete());
    }

    /** @test */
    public function updateReturnsEntityCount()
    {
        $entities = [new Article(['title' => 'Foo']), new Article(['title' => 'Bar'])];
        $result = $this->ormAddResult(Article::class, ...$entities);

        self::assertSame(2, $result->update(['title' => 'Updated']));
    }

    /** @test */
    public function insertReturnsRowCount()
    {
        $result = $this->ormAddResult(Article::class);

        self::assertSame(3, $result->insert(
            ['title' => 'A'],
            ['title' => 'B'],
            ['title' => 'C']
        ));
    }
}
