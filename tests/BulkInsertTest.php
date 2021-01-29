<?php

namespace ORM\Test;

use ORM\BulkInsert;
use ORM\Exception\InvalidArgument;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;

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
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2);
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...$articles)
            ->once()->andReturn(true);

        array_push($articles, new Article);
        $bulk->add(...$articles);
    }

    /** @test */
    public function executesUntilLimitIsNotReached()
    {
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2);
        $articles = [new Article, new Article, new Article, new Article, new Article];

        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...array_slice($articles, 0, 2))
            ->once()->andReturn(true);
        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...array_slice($articles, 2, 2))
            ->once()->andReturn(true);

        $bulk->add(...$articles);
    }

    /** @test */
    public function executesWhenNotEmpty()
    {
        $bulk = new BulkInsert($this->dbal, Article::class);
        $articles = [new Article, new Article, new Article, new Article, new Article];
        $bulk->add(...$articles);

        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...$articles)
            ->once()->andReturn(true);

        $bulk->finish();
    }

    /** @test */
    public function doesNotExecuteWhenEmpty()
    {
        $bulk = new BulkInsert($this->dbal, Article::class);

        $this->dbal->shouldNotReceive('insertAndSyncWithAutoInc');

        $bulk->finish();
    }

    /** @test */
    public function returnsTheSyncedEntities()
    {
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2);
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...$articles)
            ->once()->andReturn(true);
        $bulk->add(...$articles);

        $synced = $bulk->finish();

        self::assertSame($articles, $synced);
    }

    /** @test */
    public function executesOnSyncAfterExecution()
    {
        $onSync = \Mockery::mock(ClosureWrapper::class);
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2)->onSync($onSync);
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with(...$articles)
            ->once()->andReturn(true);

        $onSync->shouldReceive('__invoke')->with($articles)
            ->once();

        $bulk->add(...$articles);
    }

    /** @test */
    public function usesPlainInsertWithoutUpdate()
    {
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2)->noUpdates();
        $articles = [new Article, new Article];

        $this->dbal->shouldReceive('insertEntities')->with(...$articles)
            ->once()->andReturn(true);

        $bulk->add(...$articles);
    }

    /** @test */
    public function doesNotUseAutoincrement()
    {
        $bulk = (new BulkInsert($this->dbal, Article::class))->limit(2)->noAutoIncrement();
        $articles = [new Article(['id' => 23]), new Article(['id' => 42])];

        $this->dbal->shouldReceive('insertAndSync')->with(...$articles)
            ->once()->andReturn(true);

        $bulk->add(...$articles);
    }

    /** @test */
    public function doesNotUseAutoincrementByDefault()
    {
        $bulk = (new BulkInsert($this->dbal, Psr0_StudlyCaps::class))->limit(2);
        $entities = [new Psr0_StudlyCaps(['id' => 23]), new Psr0_StudlyCaps(['id' => 42])];

        $this->dbal->shouldReceive('insertAndSync')->with(...$entities)
            ->once()->andReturn(true);

        $bulk->add(...$entities);
    }
}
