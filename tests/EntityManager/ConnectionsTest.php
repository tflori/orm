<?php

namespace ORM\Test\EntityManager;

use ORM\DbConfig;
use ORM\EntityManager;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\NoConnection;
use ORM\Test\TestCase;

class ConnectionsTest extends TestCase
{
    /** @test */
    public function setConnectionAcceptsOnlyCallableArrayDbConfig()
    {
        $em = new EntityManager();
        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage(
            'Connection must be callable, DbConfig, PDO or an array of parameters for DbConfig::__constructor'
        );

        $em->setConnection('foobar');
    }

    public function provideValidConnectionSettings()
    {
        if (!extension_loaded('pdo_sqlite')) {
            return [[null]];
        }

        return [
            [['sqlite', '/tmp/test.sqlite']],
            [new DbConfig('sqlite', '/tmp/test.sqlite')],
            [new \PDO('sqlite:///tmp/test.sqlite')],
            [function () {
                return new \PDO('sqlite:///tmp/test.sqlite');
            }]
        ];
    }

    /** @dataProvider provideValidConnectionSettings
     * @test */
    public function setConnectionAccepts($value)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }
        $em = new EntityManager();

        $em->setConnection($value);

        $pdo = $em->getConnection();

        self::assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        list($i, $name, $path) = $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM);
        self::assertEquals('0', $i);
        self::assertEquals('main', $name);
        self::assertEquals('/tmp/test.sqlite', $path);
    }

    /** @test */
    public function setConnectionDoesNotCallGetter()
    {
        $em = new EntityManager();
        $mock = \Mockery::mock(\stdClass::class);
        $mock->shouldNotReceive('get');

        $em->setConnection([$mock, 'get']);
    }

    /** @test */
    public function getConnectionCallsGetterAndThrows()
    {
        $em = new EntityManager();
        $mock = \Mockery::mock(\stdClass::class);
        $mock->shouldReceive('get')->once()->andReturn('foobar');
        $em->setConnection([$mock, 'get']);
        self::expectException(NoConnection::class);
        self::expectExceptionMessage('Getter does not return PDO instance');

        $em->getConnection();
    }

    /** @test */
    public function getConnectionCallsGetterAndReturnsTheResult()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $pdo = new \PDO('sqlite:///tmp/test.sqlite');
        $em = new EntityManager();
        $em->setConnection(function () use ($pdo) {
            return $pdo;
        });

        $result = $em->getConnection();

        self::assertSame($pdo, $result);
    }

    /** @test */
    public function getConnectionThrows()
    {
        $em = new EntityManager();
        self::expectException(NoConnection::class);
        self::expectExceptionMessage('No database connection');

        $em->getConnection();
    }

    /** @test */
    public function getConnectionUsesConfiguredDbConfig()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $dbConfig = new DbConfig('sqlite', '/tmp/test.sqlite');
        $em = new EntityManager();
        $em->setConnection($dbConfig);

        $pdo = $em->getConnection();

        self::assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        list($i, $name, $path) = $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM);
        self::assertEquals('0', $i);
        self::assertEquals('main', $name);
        self::assertEquals('/tmp/test.sqlite', $path);
    }

    /** @test */
    public function configurationArray()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $em = new EntityManager();
        $em->setConnection(['sqlite', '/tmp/test.sqlite', null, null, null, null, [
            \PDO::ATTR_CASE => \PDO::CASE_LOWER
        ]]);

        $pdo = $em->getConnection();

        self::assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        list($i, $name, $path) = $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM);
        self::assertEquals('0', $i);
        self::assertEquals('main', $name);
        self::assertEquals('/tmp/test.sqlite', $path);
        self::assertSame(\PDO::CASE_LOWER, $pdo->getAttribute(\PDO::ATTR_CASE));
    }
}
