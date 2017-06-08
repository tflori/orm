<?php

namespace ORM\Test\Relation;

use ORM\EntityFetcher;
use ORM\Relation\ManyToMany;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestCase;

class ManyToManyTest extends TestCase
{
    public function testGetsReturnedByGetRelation()
    {
        $result = Article::getRelation('categories');

        self::assertInstanceOf(ManyToMany::class, $result);
    }

    public function testFetchReturnsEntityFetcher()
    {
        $entity = new Article(['id' => 42]);

        $fetcher = $entity->fetch('categories');

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    public function testFetchCreatesFetcherForTheRelatedClass()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = new EntityFetcher($this->em, Category::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    public function testFetchFiltersByRelationTable()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = \Mockery::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('join')
                ->with('"article_category"', '"article_category"."category_id" = t0.id')
                ->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('"article_category"."article_id"', 42)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    public function testFetchThrowsWhenKeyIsEmpty()
    {
        $entity = new Article([], $this->em);

        self::expectException(\ORM\Exception\IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('categories');
    }

    public function testReturnsAllWithGetAll()
    {
        $entity = new Article(['id' => 42], $this->em);
        $related = [
            $this->em->map(new Category(['id' => 12])),
            $this->em->map(new Category(['id' => 33])),
        ];
        $ids = array_map(function ($related) {
            return $related->id;
        }, $related);

        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with('SELECT "category_id" FROM "article_category" WHERE "article_id" = 42')
                  ->once()->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_NUM)->once()->andReturn($ids);

        $result = $entity->fetch('categories', true);

        self::assertSame($related, $result);
    }
}
