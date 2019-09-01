<?php

namespace ORM\Test\MockTrait;

use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\Exception\NoEntity;
use ORM\Test\Entity\Examples\Article;
use ORM\Testing\EntityFetcherMock;
use ORM\Testing\MocksEntityManager;
use PHPUnit\Framework\TestCase;

class InitMockTest extends TestCase
{
    use MocksEntityManager;

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
    public function setsOptions()
    {
        $em = $this->ormInitMock(['tableNameTemplate' => '%short%s']);

        self::assertSame('articles', $em->getNamer()->getTableName(Article::class));
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

    /** @test */
    public function createsAnEntityFetcherMock()
    {
        $em = $this->ormInitMock();

        $fetcher = $em->fetch(Article::class);

        self::assertInstanceOf(EntityFetcherMock::class, $fetcher);
    }

    /** @test */
    public function returnsMappedEntities()
    {
        $em = $this->ormInitMock();
        $em->map($original = new Article(['id' => 23]));

        $article = $em->fetch(Article::class, 23);

        self::assertSame($original, $article);
    }

    /** @test */
    public function returnsNullWithPrimaryKey()
    {
        $em = $this->ormInitMock();

        $article = $em->fetch(Article::class, 23);

        self::assertNull($article);
    }

    /** @test */
    public function throwsWhenTheClassIsNotAnEntity()
    {
        $em = $this->ormInitMock();

        self::expectException(NoEntity::class);

        $em->fetch(NoEntity::class);
    }
}
