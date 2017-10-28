<?php

namespace ORM\Test\Entity;

use ORM\Exception\InvalidConfiguration;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestEntity;
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

    /** @dataProvider provideColumnNames
     * @test */
    public function usesNamingScheme($namingScheme, $name, $expected)
    {
        TestEntity::setNamingSchemeColumn($namingScheme);

        $colName = StudlyCaps::getColumnName($name);

        self::assertSame($expected, $colName);
    }

    /** @test */
    public function storesTheNames()
    {
        TestEntity::setNamingSchemeColumn('snake_lower');
        $colNameBefore = StudlyCaps::getColumnName('StudlyCaps');
        TestEntity::setNamingSchemeColumn('StudlyCaps');

        $colName = StudlyCaps::getColumnName('StudlyCaps');

        self::assertSame($colNameBefore, $colName);
    }

    /** @test */
    public function prependsPrefix()
    {
        TestEntity::setNamingSchemeColumn('snake_lower');

        $colName = StaticTableName::getColumnName('someCol');

        self::assertSame('stn_some_col', $colName);
    }

    /** @test */
    public function doesNotDoublePrefix()
    {
        TestEntity::setNamingSchemeColumn('snake_lower');

        $colName = StaticTableName::getColumnName('stn_SomeCol');

        self::assertSame('stn_some_col', $colName);
    }

    /** @dataProvider provideColumnNames
     * @test */
    public function doesNotTouchColumnNames($namingScheme, $name)
    {
        $colName = StaticTableName::getColumnName($name);

        $second = StaticTableName::getColumnName($colName);

        self::assertSame($colName, $second);
    }

    /** @test */
    public function usesAliasIfGiven()
    {
        $colName = StaticTableName::getColumnName('foo');

        self::assertSame('bar', $colName);
    }

    /** @test */
    public function primaryKeyIsId()
    {
        $primaryKey = StudlyCaps::getPrimaryKeyVars();

        self::assertSame(['id'], $primaryKey);
    }

    /** @test */
    public function primaryKeyIsAlwaysArray()
    {
        $primaryKey = Snake_Ucfirst::getPrimaryKeyVars();

        self::assertSame(['My_Key'], $primaryKey);
    }

    /** @test */
    public function combinedPrimaryKey()
    {
        $primaryKey = StaticTableName::getPrimaryKeyVars();

        self::assertSame(['table', 'name', 'foo'], $primaryKey);
    }

    /** @test */
    public function isAutoIncrementedByDefault()
    {
        $r = StudlyCaps::isAutoIncremented();

        self::assertTrue($r);
    }

    /** @test */
    public function combinedPrimaryIsNeverAutoIncremented()
    {
        $r = StaticTableName::isAutoIncremented();

        self::assertFalse($r);
    }
}
