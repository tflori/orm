<?php

namespace ORM\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use ORM\EntityManager;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\Entity\Examples\TestEntity;

class TestCase extends MockeryTestCase
{
    /** @var EntityManager|Mock */
    protected $em;

    /** @var \PDO|Mock */
    protected $pdo;

    protected function setUp()
    {
        parent::setUp();
        TestEntity::resetStaticsForTest();
        $this->pdo = \Mockery::mock(\PDO::class);
        $this->pdo->shouldReceive('quote')->andReturnUsing(function ($var) {
            return '\'' . addslashes($var) . '\'';
        })->byDefault();
        $this->pdo->shouldReceive('query')->andReturnUsing(function ($query) {
            throw new \PDOException('Query failed by default (Query: ' . $query . ')');
        })->byDefault();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('sqlite')->byDefault();
        $this->pdo->shouldReceive('lastInsertId')->andReturn('666')->byDefault();

        $this->em = \Mockery::mock(EntityManager::class)->makePartial();
        $this->em->shouldReceive('getConnection')->andReturn($this->pdo)->byDefault();

        QueryBuilder::$defaultEntityManager = $this->em;
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->closeMockery();
    }
}
