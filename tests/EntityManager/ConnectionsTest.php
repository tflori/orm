<?php

namespace ORM\Test\EntityManager;

use ORM\DbConfig;
use ORM\EntityManager;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\NoConnection;
use ORM\Test\TestCase;

class ConnectionsTest extends TestCase
{
    public function testSetConnectionAcceptsOnlyCallableArrayDbConfig()
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

    /**
     * @dataProvider provideValidConnectionSettings
     */
    public function testSetConnectionAccepts($value)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }
        $em = new EntityManager();

        $em->setConnection($value);

        $pdo = $em->getConnection();

        self::assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        self::assertSame(
            ['0','main','/tmp/test.sqlite'],
            $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM)
        );
    }

    public function testSetConnectionDoesNotCallGetter()
    {
        $em = new EntityManager();
        $mock = \Mockery::mock(\stdClass::class);
        $mock->shouldNotReceive('get');

        $em->setConnection([$mock, 'get']);
    }

    public function testGetConnectionCallsGetterAndThrows()
    {
        $em = new EntityManager();
        $mock = \Mockery::mock(\stdClass::class);
        $mock->shouldReceive('get')->once()->andReturn('foobar');
        $em->setConnection([$mock, 'get']);
        self::expectException(NoConnection::class);
        self::expectExceptionMessage('Getter does not return PDO instance');

        $em->getConnection();
    }

    public function testGetConnectionCallsGetterAndReturnsTheResult()
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

    public function testGetConnectionThrows()
    {
        $em = new EntityManager();
        self::expectException(NoConnection::class);
        self::expectExceptionMessage('No database connection');

        $em->getConnection();
    }

    public function testGetConnectionUsesConfiguredDbConfig()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $dbConfig = new DbConfig('sqlite', '/tmp/test.sqlite');
        $em = new EntityManager();
        $em->setConnection($dbConfig);

        $pdo = $em->getConnection();

        self::assertSame('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
        self::assertSame(
            ['0','main','/tmp/test.sqlite'],
            $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM)
        );
    }

    public function testConfigurationArray()
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
        self::assertSame(
            ['0','main','/tmp/test.sqlite'],
            $pdo->query('PRAGMA DATABASE_LIST')->fetch(\PDO::FETCH_NUM)
        );
        self::assertSame(\PDO::CASE_LOWER, $pdo->getAttribute(\PDO::ATTR_CASE));
    }
}
