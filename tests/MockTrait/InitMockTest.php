<?php

namespace ORM\Test\MockTrait;

use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\MockTrait;
use PHPUnit\Framework\TestCase;

class InitMockTest extends TestCase
{
    use MockTrait;

    public function testReturnsAnEntityManager()
    {
        $em = $this->emInitMock();

        self::assertInstanceOf(EntityManager::class, $em);
    }

    public function testEntityManagerIsAMock()
    {
        $em = $this->emInitMock();

        self::assertInstanceOf(MockInterface::class, $em);
    }

    public function testMocksConnection()
    {
        $em = $this->emInitMock();
        $connection = $em->getConnection();

        self::assertInstanceOf(MockInterface::class, $em);
    }

    public function testMocksSetAttribute()
    {
        $em = $this->emInitMock();
        $connection = $em->getConnection();

        $result = $connection->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);

        self::assertTrue($result);
    }

    public function testMocksDriverName()
    {
        $em = $this->emInitMock([], 'mssql');
        $connection = $em->getConnection();

        $result = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);

        self::assertSame('mssql', $result);
    }

    public function testMocksQuote()
    {
        $em = $this->emInitMock();
        $connection = $em->getConnection();

        $result = $connection->quote('Wayne\'s World!');

        self::assertSame('\'Wayne\\\'s World!\'', $result);
    }
}
