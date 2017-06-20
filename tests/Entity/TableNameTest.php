<?php

namespace ORM\Test\Entity;

use ORM\Entity;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidName;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class TableNameTest extends TestCase
{
    public function testUsesStaticTableName()
    {
        $tableName = StaticTableName::getTableName();

        self::assertSame('my_table', $tableName);
    }

    public function testThrowsForUnknownNamingScheme()
    {
        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Naming scheme foobar unknown');

        Entity::setNamingSchemeTable('foobar');

        StudlyCaps::getTableName();
    }

    public function provideNamingScheme()
    {
        return [
            [StudlyCaps::class, 'snake_case', 'Studly_Caps'],
            [DamagedABBRVCase::class, 'snake_case', 'Damaged_ABBRV_Case'],
            [Snake_Ucfirst::class, 'snake_case', 'Snake_Ucfirst'],
            [Psr0_StudlyCaps::class, 'snake_case', 'Psr0_Studly_Caps'],

            [StudlyCaps::class, 'snake_lower', 'studly_caps'],
            [DamagedABBRVCase::class, 'snake_lower', 'damaged_abbrv_case'],
            [Snake_Ucfirst::class, 'snake_lower', 'snake_ucfirst'],
            [Psr0_StudlyCaps::class, 'snake_lower', 'psr0_studly_caps'],

            [StudlyCaps::class, 'SNAKE_UPPER', 'STUDLY_CAPS'],
            [DamagedABBRVCase::class, 'SNAKE_UPPER', 'DAMAGED_ABBRV_CASE'],
            [Snake_Ucfirst::class, 'SNAKE_UPPER', 'SNAKE_UCFIRST'],
            [Psr0_StudlyCaps::class, 'SNAKE_UPPER', 'PSR0_STUDLY_CAPS'],

            [StudlyCaps::class, 'Snake_Ucfirst', 'Studly_Caps'],
            [DamagedABBRVCase::class, 'Snake_Ucfirst', 'Damaged_ABBRV_Case'],
            [Snake_Ucfirst::class, 'Snake_Ucfirst', 'Snake_Ucfirst'],
            [Psr0_StudlyCaps::class, 'Snake_Ucfirst', 'Psr0_Studly_Caps'],

            [StudlyCaps::class, 'camelCase', 'studlyCaps'],
            [DamagedABBRVCase::class, 'camelCase', 'damagedAbbrvCase'],
            [Snake_Ucfirst::class, 'camelCase', 'snakeUcfirst'],
            [Psr0_StudlyCaps::class, 'camelCase', 'psr0StudlyCaps'],

            [StudlyCaps::class, 'StudlyCaps', 'StudlyCaps'],
            [DamagedABBRVCase::class, 'StudlyCaps', 'DamagedAbbrvCase'],
            [Snake_Ucfirst::class, 'StudlyCaps', 'SnakeUcfirst'],
            [Psr0_StudlyCaps::class, 'StudlyCaps', 'Psr0StudlyCaps'],

            [StudlyCaps::class, 'lower', 'studlycaps'],
            [DamagedABBRVCase::class, 'lower', 'damagedabbrvcase'],
            [Snake_Ucfirst::class, 'lower', 'snakeucfirst'],
            [Psr0_StudlyCaps::class, 'lower', 'psr0studlycaps'],

            [StudlyCaps::class, 'UPPER', 'STUDLYCAPS'],
            [DamagedABBRVCase::class, 'UPPER', 'DAMAGEDABBRVCASE'],
            [Snake_Ucfirst::class, 'UPPER', 'SNAKEUCFIRST'],
            [Psr0_StudlyCaps::class, 'UPPER', 'PSR0STUDLYCAPS'],
        ];
    }

    /**
     * @dataProvider provideNamingScheme
     */
    public function testTableNamingByNamingScheme($class, $namingScheme, $expected)
    {
        Entity::setTableNameTemplate('%short%');
        Entity::setNamingSchemeTable($namingScheme);

        /** @var Entity $class */
        $tableName = $class::getTableName();

        self::assertSame($expected, $tableName);
    }

    public function testThrowsForIllegalTemplates()
    {
        Entity::setTableNameTemplate('%foobar%');

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Template invalid: Placeholder %foobar% is not allowed');

        StudlyCaps::getTableName();
    }

    public function provideTemplate()
    {
        return [
            [StudlyCaps::class, '%short%', 'Studly_Caps'],
            [DamagedABBRVCase::class, '%short%', 'Damaged_ABBRV_Case'],
            [Snake_Ucfirst::class, '%short%', 'Snake_Ucfirst'],
            [Psr0_StudlyCaps::class, '%short%', 'Psr0_Studly_Caps'],

            [StudlyCaps::class, '%namespace%', 'ORM_Test_Entity_Examples'],
            [StudlyCaps::class, '%namespace[2]%', 'Entity'],
            [StudlyCaps::class, '%namespace[2*]%', 'Entity_Examples'],
            [StudlyCaps::class, '%namespace[4]%', ''],

            [StudlyCaps::class, '%name%', 'ORM_Test_Entity_Examples_Studly_Caps'],
            [StudlyCaps::class, '%name[2]%', 'Entity'],
            [StudlyCaps::class, '%name[2*]%', 'Entity_Examples_Studly_Caps'],
            [StudlyCaps::class, '%name[4]%', 'Studly_Caps'],
            [StudlyCaps::class, '%name[5]%', ''],

            [Psr0_StudlyCaps::class, '%name[-2]%', 'Psr0'],
            [Psr0_StudlyCaps::class, '%name[-2*]%', 'Psr0_Studly_Caps'],
            [Psr0_StudlyCaps::class, '%name[0]%_%name[-1]%', 'ORM_Studly_Caps'],
            [Psr0_StudlyCaps::class, '"%name[0]%"."%name[-1]%"', '"ORM"."Studly_Caps"'],
        ];
    }

    /**
     * @dataProvider provideTemplate
     */
    public function testTableNamingByTemplate($class, $template, $expected)
    {
        Entity::setTableNameTemplate($template);
        Entity::setNamingSchemeTable('snake_case');

        if ($expected === '') {
            self::expectException(InvalidName::class);
            self::expectExceptionMessage('Table name can not be empty');
        }

        /** @var Entity $class */
        $tableName = $class::getTableName();

        self::assertSame($expected, $tableName);
    }
}
