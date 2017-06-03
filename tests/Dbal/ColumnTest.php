<?php

namespace ORM\Test\Dbal;

use ORM\Dbal\Column;
use ORM\Dbal\Type\Number;
use ORM\Test\TestCase;

class ColumnTest extends TestCase
{
    public function provideColumnDefinitions()
    {
        $colDef = [
            'column_name' => 'test_col',
            'type' => Number::class,
            'data_type' => 'tinyint',
            'column_default' => '1',
            'is_nullable' => 'YES',
        ];

        return [
            [$colDef, 'data_type', 'tinyint'],
            [$colDef, 'name', 'test_col'],
            [$colDef, 'default', '1'],
            [$colDef, 'nullable', true],
        ];
    }

    /**
     * @dataProvider provideColumnDefinitions
     */
    public function testMagicGetter($columnDefinition, $var, $expected)
    {
        $column = new Column($this->dbal, $columnDefinition);

        $result = $column->$var;

        self::assertSame($expected, $result);
    }
}
