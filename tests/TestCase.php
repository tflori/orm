<?php

namespace ORM\Test;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\Mock;
use ORM\Dbal;
use ORM\EntityManager;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\Constraint\ArraySubset;
use PHPUnit\Framework\InvalidArgumentException;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->initMocks();
    }

    protected function initMocks()
    {
        TestEntity::resetStaticsForTest();
        TestEntityManager::resetStaticsForTest();

        $this->pdo = $this->mocks['pdo'] = m::mock(\PDO::class);
        $this->pdo->shouldReceive('quote')->andReturnUsing(function ($var) {
            return '\'' . addslashes($var) . '\'';
        })->byDefault();
        $this->pdo->shouldReceive('query')->andReturnUsing(function ($query) {
            throw new \PDOException('Query failed by default (Query: ' . $query . ')');
        })->byDefault();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mssql')->byDefault();
        $this->pdo->shouldReceive('lastInsertId')->andReturn('666')->byDefault();

        $this->em = $this->mocks['em'] = m::mock(TestEntityManager::class, [])->makePartial();
        $this->em->shouldReceive('getConnection')->andReturnUsing(function () {
            return $this->pdo;
        })->byDefault();

        $this->dbal = $this->mocks['dbal'] = m::mock(Dbal\Mysql::class, [$this->em])->makePartial();
        $this->em->shouldReceive('getDbal')->andReturnUsing(function () {
            return $this->dbal;
        })->byDefault();

        QueryBuilder::$defaultEntityManager = $this->em;
    }

    /**
     * Performs assertions shared by all tests of a test case. This method is
     * called before execution of a test ends and before the tearDown method.
     */
    protected function assertPostConditions(): void
    {
        $this->addMockeryExpectationsToAssertionCount();
        $this->closeMockery();

        parent::assertPostConditions();
    }

    protected function addMockeryExpectationsToAssertionCount()
    {
        $container = m::getContainer();
        if ($container != null) {
            $count = $container->mockery_getExpectationCount();
            $this->addToAssertionCount($count);
        }
    }

    protected function closeMockery()
    {
        m::close();
    }

    protected static function getProtectedProperty($object, $property)
    {
        $reflection = new ReflectionClass($object);

        if (!$reflection->hasProperty($property)) {
            return null;
        }

        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);
        $value = $propertyReflection->getValue($object);
        $propertyReflection->setAccessible(false);
        return $value;
    }

    protected static function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($object, $value);
        $propertyReflection->setAccessible(false);
    }

    /**
     * Asserts that an array has a specified subset.
     *
     * @param array|\ArrayAccess $subset
     * @param array|\ArrayAccess $array
     * @param bool $strict Check for object identity
     * @param float|null $delta
     * @param string $message
     */
    public static function assertArraySubset(
        $subset,
        $array,
        bool $strict = false,
        string $message = '',
        float $delta = null
    ): void {
        if (!is_array($subset)) {
            throw InvalidArgumentException::create(1, 'array or ArrayAccess');
        }

        if (!is_array($array)) {
            throw InvalidArgumentException::create(2, 'array or ArrayAccess');
        }

        $constraint = new ArraySubset($subset, $strict, $delta);

        static::assertThat($array, $constraint, $message);
    }
}
