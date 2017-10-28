<?php

namespace ORM\Test\MockTrait;

use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\MockTrait;
use PHPUnit\Framework\TestCase;

class InitMockTest extends TestCase
{
    use MockTrait;

    /** @test */
    public function returnsAnEntityManager()
    {
        $em = $this->ormInitMock();

        self::assertInstanceOf(EntityManager::class, $em);
    }

    /** @test */
    public function entityManagerIsAMock()
    {
        $em = $this->ormInitMock();

        self::assertInstanceOf(MockInterface::class, $em);
    }

    /** @test */
    public function mocksConnection()
    {
        $em = $this->ormInitMock();
        $connection = $em->getConnection();

        self::assertInstanceOf(MockInterface::class, $em);
    }

    /** @test */
    public function mocksSetAttribute()
    {
        $em = $this->ormInitMock();
        $connection = $em->getConnection();

        $result = $connection->setAttribute(\PDO::ATTR_AUTOCOMMIT, true);

        self::assertTrue($result);
    }

    /** @test */
    public function mocksDriverName()
    {
        $em = $this->ormInitMock([], 'mssql');
        $connection = $em->getConnection();

        $result = $connection->getAttribute(\PDO::ATTR_DRIVER_NAME);

        self::assertSame('mssql', $result);
    }

    /** @test */
    public function mocksQuote()
    {
        $em = $this->ormInitMock();
        $connection = $em->getConnection();

        $result = $connection->quote('Wayne\'s World!');

        self::assertSame('\'Wayne\\\'s World!\'', $result);
    }
}
