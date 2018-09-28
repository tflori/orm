<?php

namespace ORM\Test\EntityManager;

use Mockery as m;
use ORM\BulkInsert;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestCase;

class BulkInsertTest extends TestCase
{
    /** @var BulkInsert|m\Mock */
    protected $bulkInsert;

    protected function setUp()
    {
        parent::setUp();

        $this->mocks['bulkInsert'] = $this->bulkInsert = m::mock(BulkInsert::class, [$this->dbal, Article::class])
            ->makePartial();
        $this->em->setBulkInsert(Article::class, $this->bulkInsert);
    }

    /** @test */
    public function createsABulkInsert()
    {
        $onSync = function ($synced) {
        };

        $bulkInsert = $this->em->useBulkInserts(Category::class, false, 30, $onSync);

        self::assertEquals(new BulkInsert($this->dbal, Category::class, false, 30, $onSync), $bulkInsert);
    }

    /** @test */
    public function returnsExistingBulkInsert()
    {
        $bulkInsert = new BulkInsert($this->dbal, Article::class);
        $this->em->setBulkInsert(Article::class, $bulkInsert);

        $result = $this->em->useBulkInserts(Article::class, false, 30);

        self::assertSame($bulkInsert, $result);
    }

    /** @test */
    public function insertAddsToBulkInsert()
    {
        $entity = new Article;
        $this->bulkInsert->shouldReceive('add')->with($entity)
            ->once();

        $this->em->insert($entity);
    }

    /** @test */
    public function insertOtherEntitiesNot()
    {
        $entity = new Category;
        $this->bulkInsert->shouldNotReceive('add');
        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with($entity)
            ->once()->andReturn($entity);

        $this->em->insert($entity);
    }

    /** @test */
    public function finishesBulkImport()
    {
        $synced = [new Article];
        $this->bulkInsert->shouldReceive('finish')->with()
            ->once()->andReturn($synced);

        $result = $this->em->finishBulkInserts(Article::class);

        self::assertSame($synced, $result);
    }

    /** @test */
    public function removesTheBulkImport()
    {
        $this->bulkInsert->shouldReceive('finish')->with()
            ->once()->andReturn([]);

        $this->em->finishBulkInserts(Article::class);

        $entity = new Article;
        $this->bulkInsert->shouldNotReceive('add');
        $this->dbal->shouldReceive('insertAndSyncWithAutoInc')->with($entity)
            ->once()->andReturn($entity);

        $this->em->insert($entity);
    }
}
