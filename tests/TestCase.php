<?php

namespace ORM\Test;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use ORM\Dbal;
use ORM\EntityManager;
use ORM\QueryBuilder\QueryBuilder;
use ReflectionClass;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var EntityManager|Mock */
    protected $em;

    /** @var \PDO|Mock */
    protected $pdo;

    /** @var Dbal\Mysql|Mock */
    protected $dbal;

    /** @var Mock[] */
    protected $mocks = [];

    protected function setUp()
    {
        parent::setUp();
        TestEntity::resetStaticsForTest();
        TestEntityManager::resetStaticsForTest();

        $this->mocks['pdo'] = $this->pdo = \Mockery::mock(\PDO::class);
        $this->pdo->shouldReceive('quote')->andReturnUsing(function ($var) {
            return '\'' . addslashes($var) . '\'';
        })->byDefault();
        $this->pdo->shouldReceive('query')->andReturnUsing(function ($query) {
            throw new \PDOException('Query failed by default (Query: ' . $query . ')');
        })->byDefault();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mssql')->byDefault();
        $this->pdo->shouldReceive('lastInsertId')->andReturn('666')->byDefault();

        $this->mocks['em'] = $this->em = \Mockery::mock(TestEntityManager::class, [])->makePartial();
        $this->em->shouldReceive('getConnection')->andReturn($this->pdo)->byDefault();

        $this->mocks['dbal'] = $this->dbal = \Mockery::mock(Dbal\Mysql::class, [$this->em])->makePartial();
        $this->em->shouldReceive('getDbal')->andReturn($this->dbal)->byDefault();

        QueryBuilder::$defaultEntityManager = $this->em;
    }

    /**
     * Performs assertions shared by all tests of a test case. This method is
     * called before execution of a test ends and before the tearDown method.
     */
    protected function assertPostConditions()
    {
        $this->addMockeryExpectationsToAssertionCount();
        $this->closeMockery();

        parent::assertPostConditions();
    }

    protected function addMockeryExpectationsToAssertionCount()
    {
        $container = \Mockery::getContainer();
        if ($container != null) {
            $count = $container->mockery_getExpectationCount();
            $this->addToAssertionCount($count);
        }
    }

    protected function closeMockery()
    {
        \Mockery::close();
    }

    protected static function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($object, $value);
        $propertyReflection->setAccessible(false);
    }
}
