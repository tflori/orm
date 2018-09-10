<?php

namespace ORM\Test;

use ORM\BulkInsert;
use ORM\Exception\InvalidArgument;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;

class BulkInsertTest extends TestCase
{
    /** @test */
    public function acceptsOnlyEntitiesFromGivenType()
    {
        $bulk = new BulkInsert($this->dbal, Article::class);

        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('Only entities from type ' . Article::class . ' can be added');

        $bulk->add(new Article, new Category);
    }

    /** @test */
    public function executesWhenLimitReached()
    {
        $bulk = new BulkInsert($this->dbal, Article::class, true, 2);
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('bulkInsert')->with($articles, true)
            ->once()->andReturn($articles);

        array_push($articles, new Article);
        $bulk->add(...$articles);
    }

    /** @test */
    public function executesUntilLimitIsNotReached()
    {
        $bulk = new BulkInsert($this->dbal, Article::class, true, 2);
        $articles = [new Article, new Article, new Article, new Article, new Article];

        $this->dbal->shouldReceive('bulkInsert')->with(array_slice($articles, 0, 2), true)
            ->once()->andReturn(array_slice($articles, 0, 2));
        $this->dbal->shouldReceive('bulkInsert')->with(array_slice($articles, 2, 2), true)
            ->once()->andReturn(array_slice($articles, 2, 2));

        $bulk->add(...$articles);
    }

    /** @test */
    public function executesWhenNotEmpty()
    {
        $bulk = new BulkInsert($this->dbal, Article::class);
        $articles = [new Article, new Article, new Article, new Article, new Article];
        $bulk->add(...$articles);

        $this->dbal->shouldReceive('bulkInsert')->with($articles, true)
            ->once()->andReturn($articles);

        $bulk->finish();
    }

    /** @test */
    public function doesNotExecuteWhenEmpty()
    {
        $bulk = new BulkInsert($this->dbal, Article::class);

        $this->dbal->shouldNotReceive('bulkInsert');

        $bulk->finish();
    }

    /** @test */
    public function returnsTheSyncedEntities()
    {
        $bulk = new BulkInsert($this->dbal, Article::class, true, 2);
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('bulkInsert')->with($articles, true)
            ->once()->andReturn([new Article, new Article]);
        $bulk->add(...$articles);

        $synced = $bulk->finish();

        self::assertNotSame($articles, $synced);
        self::assertEquals($articles, $synced);
    }

    /** @test */
    public function executesOnSyncAfterExecution()
    {
        $onSync = \Mockery::mock(ClosureWrapper::class);
        $bulk = new BulkInsert($this->dbal, Article::class, true, 2, $onSync);
        $articles = [new Article, new Article];
        $synced = [new Article, new Article];

        $this->dbal->shouldReceive('bulkInsert')->with($articles, true)
            ->once()->andReturn($synced);

        $onSync->shouldReceive('__invoke')->with($synced)
            ->once();

        $bulk->add(...$articles);
    }
}
