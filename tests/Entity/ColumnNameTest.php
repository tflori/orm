<?php

namespace ORM\Test\Entity;

use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\Entity\Examples\TestEntity;
use ORM\Test\TestCase;

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
        TestEntity::$namingSchemeColumn = $namingScheme;

        $colName = StudlyCaps::getColumnName($name);

        self::assertSame($expected, $colName);
    }

    public function testStoresTheNames()
    {
        TestEntity::$namingSchemeColumn = 'snake_lower';
        $colNameBefore                  = StudlyCaps::getColumnName('StudlyCaps');
        TestEntity::$namingSchemeColumn = 'StudlyCaps';

        $colName = StudlyCaps::getColumnName('StudlyCaps');

        self::assertSame($colNameBefore, $colName);
    }

    public function testPrependsPrefix()
    {
        TestEntity::$namingSchemeColumn = 'snake_lower';

        $colName = StaticTableName::getColumnName('someCol');

        self::assertSame('stn_some_col', $colName);
    }

    public function testDoesNotDoublePrefix()
    {
        TestEntity::$namingSchemeColumn = 'snake_lower';

        $colName = StaticTableName::getColumnName('stn_SomeCol');

        self::assertSame('stn_some_col', $colName);
    }

    /**
     * @dataProvider provideColumnNames
     */
    public function testDoesNotTouchColumnNames($namingScheme, $name)
    {
        TestEntity::$namingSchemeColumn = $namingScheme;
        $colName                        = StaticTableName::getColumnName($name);

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
        $primaryKey = StudlyCaps::getPrimaryKeyVars();

        self::assertSame(['id'], $primaryKey);
    }

    public function testPrimaryKeyIsAlwaysArray()
    {
        $primaryKey = Snake_Ucfirst::getPrimaryKeyVars();

        self::assertSame(['My_Key'], $primaryKey);
    }

    public function testCombinedPrimaryKey()
    {
        $primaryKey = StaticTableName::getPrimaryKeyVars();

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
}
