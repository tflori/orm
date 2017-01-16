<?php

namespace ORM\Test;

use ORM\DbConfig;

class DbConfigTest extends TestCase
{
    public function provideAttributes()
    {
        return [
            [
                ['mysql', 'db'],
                'mysql:host=localhost;port=3306;dbname=db'
            ],
            [
                ['mysql', 'db', null, null, '/run/mysqld/mysqld.sock'],
                'mysql:unix_socket=/run/mysqld/mysqld.sock;dbname=db'
            ],
            [
                ['pgsql', 'db'],
                'pgsql:host=localhost;port=5432;dbname=db'
            ],
            [
                ['sqlite', '/tmp/test.sqlite'],
                'sqlite:/tmp/test.sqlite'
            ]
        ];
    }

    /**
     * @dataProvider provideAttributes
     */
    public function testConstructorSetsDsn($args, $expectedDsn)
    {
        if ($args[0] === 'mysql' && !extension_loaded('pdo_mysql')) {
            self::markTestSkipped('pdo_mysql extension required');
        } elseif ($args[0] === 'pgsql' && !extension_loaded('pdo_pgsql')) {
            self::markTestSkipped('pdo_pgsql extension required');
        } elseif ($args[0] === 'sqlite' && !extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite extension required');
        }

        $dbConfigReflection = new \ReflectionClass(DbConfig::class);

        /** @var DbConfig $dbConfig */
        $dbConfig = $dbConfigReflection->newInstanceArgs($args);

        self::assertSame($expectedDsn, $dbConfig->getDsn());
    }
}
