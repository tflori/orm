<?php

namespace ORM\Test\EntityManager;

use ORM\EntityManager;
use ORM\Test\Entity\Examples\TestEntity;
use ORM\Test\TestCase;

class OptionsTest extends TestCase
{
    public function provideOptions()
    {
        return [
            [EntityManager::OPT_IDENTIFIER_DIVIDER, '.'],
            [EntityManager::OPT_QUOTING_CHARACTER, '`'],
            [EntityManager::OPT_MYSQL_BOOLEAN_FALSE, "'no'"],
            [EntityManager::OPT_MYSQL_BOOLEAN_TRUE, "'yes'"]
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

    public function testSetsConnectionOnConstruct()
    {
        $emMock = \Mockery::mock(EntityManager::class)->makePartial();
        $emMock->shouldReceive('setConnection')->with('something')->once();

        $emMock->__construct([
            EntityManager::OPT_CONNECTION => 'something'
        ]);
    }

    public function provideEntityStatics()
    {
        return [
            [EntityManager::OPT_TABLE_NAME_TEMPLATE, 'tableNameTemplate', '%namespace%'],
            [EntityManager::OPT_NAMING_SCHEME_TABLE, 'namingSchemeTable', 'StudlyCaps'],
            [EntityManager::OPT_NAMING_SCHEME_COLUMN, 'namingSchemeColumn', 'StudlyCaps'],
            [EntityManager::OPT_NAMING_SCHEME_METHODS, 'namingSchemeMethods', 'snake_case'],
        ];
    }

    /**
     * @dataProvider provideEntityStatics
     */
    public function testSetsEntityStaticsOnConstruct($option, $static, $value)
    {
        $em = new EntityManager([
            $option => $value
        ]);

        self::assertSame($value, call_user_func([TestEntity::class, 'get' . ucfirst($static)]));
    }
}
