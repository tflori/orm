<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Column;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NotValid;
use ORM\Dbal\Mysql;
use ORM\Dbal\Type\Boolean;
use ORM\EntityManager;
use ORM\Test\TestCase;

class BooleanTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Boolean::class));
    }

    public function provideValues()
    {
        return [
            ['1', '0', true, true],
            ['1', '0', false, true],

            ['1', '0', '1', true],
            ['1', '0', '0', true],
            ['1', '0', 0, true],
            ['1', '0', 1, true],
            ['true', 'false', 'true', true],
            ['true', 'false', 'false', true],
            ['\'y\'', '\'n\'', 'y', true],
            ['\'y\'', '\'n\'', 'n', true],

            ['1', '0', 't', 't can not be converted to boolean'],
            ['1', '0', 'f', 'f can not be converted to boolean'],
            ['true', 'false', '1', '1 can not be converted to boolean'],
            ['true', 'false', '0', '0 can not be converted to boolean'],
            ['true', 'false', 1, '1 can not be converted to boolean'],
            ['true', 'false', 0, '0 can not be converted to boolean'],
            ['\'y\'', '\'n\'', 'true', 'true can not be converted to boolean'],
            ['\'y\'', '\'n\'', 'false', 'false can not be converted to boolean'],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($true, $false, $value, $expected)
    {
        $this->dbal->setOption(EntityManager::OPT_BOOLEAN_TRUE, $true);
        $this->dbal->setOption(EntityManager::OPT_BOOLEAN_FALSE, $false);
        $column = new Column($this->dbal, [
            'column_name' => 'abool',
            'type' => Boolean::class,
            'data_type' => 'tinyint',
        ]);
        $type = $column->getType();

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
