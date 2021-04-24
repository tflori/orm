<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UndefinedRelation;
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
        self::expectExceptionMessage('The opponent of a many to many relation has to be a many to many relation');

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
}
