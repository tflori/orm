<?php

namespace ORM\Test\EntityManager;

use ORM\Dbal\Dbal;
use ORM\EntityManager;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\TestEntity;
use ORM\Test\TestCase;

class OptionsTest extends TestCase
{
    public function provideOptions()
    {
        return [
            [EntityManager::OPT_IDENTIFIER_DIVIDER, '.'],
            [EntityManager::OPT_QUOTING_CHARACTER, '`'],
            [EntityManager::OPT_BOOLEAN_FALSE, "'no'"],
            [EntityManager::OPT_BOOLEAN_TRUE, "'yes'"]
        ];
    }

    /**
     * @dataProvider provideOptions
     */
    public function testSetsOptionsOnConstruct($option, $value)
    {
        $emMock = \Mockery::mock(EntityManager::class)->makePartial();

        $emMock->shouldReceive('setOption')->with($option, $value)->once();

        $emMock->__construct([
            $option => $value
        ]);
    }

    /**
     * @dataProvider provideOptions
     */
    public function testSetOptionStores($option, $value)
    {
        $this->em->setOption($option, $value);

        self::assertSame($value, $this->em->getOption($option));
    }


    public function provideDbalStatics()
    {
        return [
            [EntityManager::OPT_IDENTIFIER_DIVIDER, '|',
                'escapeIdentifier', 'db|table', '"db"|"table"'],
            [EntityManager::OPT_QUOTING_CHARACTER, '`',
                'escapeIdentifier', 'db.table', '`db`.`table`'],
            [EntityManager::OPT_BOOLEAN_FALSE, "'no'",
                'escapeValue', false, "'no'"],
            [EntityManager::OPT_BOOLEAN_TRUE, "'yes'",
                'escapeValue', true, "'yes'"]
        ];
    }

    /**
     * @dataProvider provideDbalStatics
     */
    public function testSetsStaticsFromDbal($option, $value, $method, $param, $expected)
    {
        $this->em->setOption($option, $value);
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->once()
            ->andReturn('sqlite');

        $dbal = $this->em->getDbal();
        $result = call_user_func([$dbal, $method], $param);

        self::assertSame($expected, $result);
    }

    public function testSetsConnectionOnConstruct()
    {
        $emMock = \Mockery::mock(EntityManager::class)->makePartial();
        $emMock->shouldReceive('setConnection')->with('something')->once();

        $emMock->__construct([
            EntityManager::OPT_CONNECTION => 'something'
        ]);
    }

    public function provideDeprecatedOptions()
    {
        return [
            [EntityManager::OPT_MYSQL_BOOLEAN_TRUE, EntityManager::OPT_BOOLEAN_TRUE, '\'y\''],
            [EntityManager::OPT_MYSQL_BOOLEAN_FALSE, EntityManager::OPT_BOOLEAN_FALSE, '\'n\''],
            [EntityManager::OPT_PGSQL_BOOLEAN_TRUE, EntityManager::OPT_BOOLEAN_TRUE, '\'y\''],
            [EntityManager::OPT_PGSQL_BOOLEAN_FALSE, EntityManager::OPT_BOOLEAN_FALSE, '\'n\''],
            [EntityManager::OPT_SQLITE_BOOLEAN_TRUE, EntityManager::OPT_BOOLEAN_TRUE, '\'y\''],
            [EntityManager::OPT_SQLITE_BOOLEAN_FALSE, EntityManager::OPT_BOOLEAN_FALSE, '\'n\''],
        ];
    }

    /**
     * @dataProvider provideDeprecatedOptions
     */
    public function testConvertsDeprecatedOptions($deprecated, $actual, $value)
    {
        $em = new EntityManager([$deprecated => $value]);

        $result = $em->getOption($actual);

        self::assertSame($value, $result);
    }
}
