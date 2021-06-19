<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UndefinedRelation;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Relation\ManyToMany;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestCase;
use PDOStatement;

class ManyToManyTest extends TestCase
{
    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = Article::getRelation('categories');

        self::assertInstanceOf(ManyToMany::class, $result);
    }

    /** @test */
    public function fetchReturnsEntityFetcher()
    {
        $entity = new Article(['id' => 42]);

        $fetcher = $entity->fetch('categories');

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    /** @test */
    public function fetchCreatesFetcherForTheRelatedClass()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = new EntityFetcher($this->em, Category::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    /** @test */
    public function fetchFiltersByRelationTable()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = m::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('join')
                ->with('"article_category"', '"article_category"."category_id" = t0.id')
                ->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('"article_category"."article_id"', 42)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    /** @test */
    public function fetchThrowsWhenKeyIsEmpty()
    {
        $entity = new Article([], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('categories');
    }

    /** @test */
    public function throwsWhenTheOpponentIsNotDefined()
    {
        $relation = new ManyToMany(Article::class, ['id' => 'article_id'], 'related', 'related_articles');

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation related is not defined');

        $relation->fetch(new Article(), $this->em);
    }

    /** @test */
    public function throwsWhenTheOpponentIsNotManyToMany()
    {
        $relation = new ManyToMany(Article::class, ['id' => 'article_id'], 'writer', 'related_articles');

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('The opponent of a ManyToMany relation has to be a ManyToMany relation');

        $relation->fetch(new Article(), $this->em);
    }

    /** @test */
    public function returnsPreviouslyMappedWithGetAll()
    {
        $entity = new Article(['id' => 42], $this->em);
        $related = [
            $this->em->map(new Category(['id' => 12])),
            $this->em->map(new Category(['id' => 33])),
        ];

        $this->pdo->shouldReceive('query')->with(
            'SELECT DISTINCT t0.* FROM "category" AS t0' .
            ' JOIN "article_category" ON "article_category"."category_id" = "t0"."id"' .
            ' WHERE "article_category"."article_id" = 42'
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->with()->times(3)
            ->andReturn(
                ['id' => 12, 'name' => 'Foos'],
                ['id' => 33, 'name' => 'Bars'],
                false
            );

        $result = $entity->fetch('categories', true);

        self::assertSame($related, $result);
    }

    /** @test */
    public function fetchesAllEntitiesWithOneQuery()
    {
        $entity = new Article(['id' => 42], $this->em);

        $this->pdo->shouldReceive('query')->with(
            'SELECT DISTINCT t0.* FROM "category" AS t0' .
            ' JOIN "article_category" ON "article_category"."category_id" = "t0"."id"' .
            ' WHERE "article_category"."article_id" = 42'
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->with()->times(3)
            ->andReturn(
                ['id' => 1, 'name' => 'Foos'],
                ['id' => 2, 'name' => 'Bars'],
                false
            );

        $result = $entity->fetch('categories', true);

        self::assertEquals([
            new Category(['id' => 1, 'name' => 'Foos'], null, true),
            new Category(['id' => 2, 'name' => 'Bars'], null, true),
        ], $result);
    }

    /** @test */
    public function eagerLoadFetchesAllOpponentsWithTwoQueries()
    {
        $articles = [
            new Article(['id' => 3, 'text' => 'Hello Jane!']),
            new Article(['id' => 4, 'text' => 'Hello John!']),
            new Article(['id' => 5, 'text' => 'Hello Carl!']),
            new Article(['id' => 6, 'text' => 'Hello Carl!']),
        ];

        // query 1 to get the mapping data
        $this->em->shouldReceive('query')->with('"article_category"', 't0')->once()
            ->andReturn($query = m::mock(QueryBuilder::class, ['"article_category"', 't0', $this->em])->makePartial());
        $query->shouldReceive('whereIn')->with(['t0."article_id"'], [
            ['article_id' => 3], ['article_id' => 4], ['article_id' => 5], ['article_id' => 6]
        ])->once()->andReturnSelf();
        $query->shouldReceive('all')->with()->once()->andReturn([
            ['article_id' => 3, 'category_id' => 1],
            ['article_id' => 3, 'category_id' => 2],
            ['article_id' => 4, 'category_id' => 2],
            ['article_id' => 5, 'category_id' => 3],
        ]);

        // query 2 to get the entities
        $this->em->shouldReceive('fetch')->with(Category::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class, [$this->em, Category::class])->makePartial());
        $fetcher->shouldReceive('whereIn')->with(['id'], [
            ['category_id' => 1],
            ['category_id' => 2],
            ['category_id' => 3]
        ])->once()->andReturnSelf();
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                new Category(['id' => 1, 'name' => 'Science']),
                new Category(['id' => 2, 'name' => 'Fiction']),
                new Category(['id' => 3, 'name' => 'Misc']),
            ]);

        $this->em->eagerLoad('categories', ...$articles);
    }

    /** @test */
    public function eagerLoadAssignsCategoriesToArticles()
    {
        $articles = [
            $article3 = new Article(['id' => 3, 'text' => 'Hello Jane!']),
            $article4 = new Article(['id' => 4, 'text' => 'Hello John!']),
            $article5 = new Article(['id' => 5, 'text' => 'Hello Carl!']),
            $article6 = new Article(['id' => 6, 'text' => 'Hello Carl!']),
        ];

        $this->em->shouldReceive('query')->with('"article_category"', 't0')
            ->andReturn($query = m::mock(QueryBuilder::class, ['"article_category"', 't0', $this->em])->makePartial());
        $query->shouldReceive('all')->with()->andReturn([
            ['article_id' => 3, 'category_id' => 1],
            ['article_id' => 3, 'category_id' => 2],
            ['article_id' => 4, 'category_id' => 2],
            ['article_id' => 5, 'category_id' => 3],
        ]);

        $this->em->shouldReceive('fetch')->with(Category::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class, [$this->em, Category::class])->makePartial());
        $fetcher->shouldReceive('all')->with()
            ->andReturn([
                $category1 = new Category(['id' => 1, 'name' => 'Science']),
                $category2 = new Category(['id' => 2, 'name' => 'Fiction']),
                $category3 = new Category(['id' => 3, 'name' => 'Misc']),
            ]);

        $this->em->eagerLoad('categories', ...$articles);

        self::assertSame([$category1, $category2], $article3->categories);
        self::assertSame([$category2], $article4->categories);
        self::assertSame([$category3], $article5->categories);
        self::assertSame([], $article6->categories);
    }
}
