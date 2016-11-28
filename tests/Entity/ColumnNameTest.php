<?php

namespace ORM\Test\Entity;

use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\Entity\Examples\TestEntity;

class ColumnNameTest extends TestCase
{
    public function provideColumnNames()
    {
        return [
            ['StudlyCaps', 'StudlyCaps', 'StudlyCaps'],
            ['StudlyCaps', 'camelCase', 'CamelCase'],
            ['StudlyCaps', 'snake_CASE', 'SnakeCase'],

            ['snake_case', 'snake_CASE', 'snake_CASE'],
            ['snake_case', 'ORMManager', 'ORM_Manager'],
            ['snake_case', 'ManagerORM', 'Manager_ORM'],
            ['snake_case', 'hasADog', 'has_A_Dog'],

            ['snake_lower', 'snake_CASE', 'snake_case'],
            ['snake_lower', 'ORMManager', 'orm_manager'],
            ['snake_lower', 'ManagerORM', 'manager_orm'],
            ['snake_lower', 'hasADog', 'has_a_dog'],
        ];
    }

    /**
     * @dataProvider provideColumnNames
     */
    public function testUsesNamingScheme($namingScheme, $name, $expected)
    {
        TestEntity::$namingSchemeDb = $namingScheme;

        $colName = StudlyCaps::getColumnName($name);

        self::assertSame($expected, $colName);
    }

    public function testStoresTheNames()
    {
        TestEntity::$namingSchemeDb = 'snake_lower';
        $colNameBefore = StudlyCaps::getColumnName('StudlyCaps');
        TestEntity::$namingSchemeDb = 'StudlyCaps';

        $colName = StudlyCaps::getColumnName('StudlyCaps');

        self::assertSame($colNameBefore, $colName);
    }

    public function testPrependsPrefix()
    {
        TestEntity::$namingSchemeDb = 'snake_lower';

        $colName = StaticTableName::getColumnName('someCol');

        self::assertSame('stn_some_col', $colName);
    }

    public function testDoesNotDoublePrefix()
    {
        TestEntity::$namingSchemeDb = 'snake_lower';

        $colName = StaticTableName::getColumnName('stn_SomeCol');

        self::assertSame('stn_some_col', $colName);
    }

    /**
     * @dataProvider provideColumnNames
     */
    public function testDoesNotTouchColumnNames($namingScheme, $name, $expected)
    {
        TestEntity::$namingSchemeDb = $namingScheme;
        $colName = StaticTableName::getColumnName($name);

        $second = StaticTableName::getColumnName($colName);

        self::assertSame($colName, $second);
    }

    public function testUsesAliasIfGiven()
    {
        $colName = StaticTableName::getColumnName('foo');

        self::assertSame('bar', $colName);
    }

    public function testPrimaryKeyIsId()
    {
        $primaryKey = StudlyCaps::getPrimaryKey();

        self::assertSame(['id'], $primaryKey);
    }

    public function testPrimaryKeyIsAlwaysArray()
    {
        $primaryKey = Snake_Ucfirst::getPrimaryKey();

        self::assertSame(['My_Key'], $primaryKey);
    }

    public function testCombinedPrimaryKey()
    {
        $primaryKey = StaticTableName::getPrimaryKey();

        self::assertSame(['table', 'name', 'foo'], $primaryKey);
    }

    public function testIsAutoIncrementedByDefault()
    {
        $r = StudlyCaps::isAutoIncremented();

        self::assertTrue($r);
    }

    public function testCombinedPrimaryIsNeverAutoIncremented()
    {
        $r = StaticTableName::isAutoIncremented();

        self::assertFalse($r);
    }

    public function testAutoIncrementSequenceByDefault()
    {
        $sequence = StudlyCaps::getAutoIncrementSequence();

        self::assertSame(
            StudlyCaps::getTableName() . '_' . StudlyCaps::getColumnName(StudlyCaps::getPrimaryKey()[0] . '_seq'),
            $sequence
        );
    }

    public function testCustomAutoIncrementSequence()
    {
        $sequence = Snake_Ucfirst::getAutoIncrementSequence();

        self::assertSame('snake_ucfirst_seq', $sequence);
    }
}
