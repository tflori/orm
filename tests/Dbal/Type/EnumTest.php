<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Enum;
use ORM\Test\TestCase;

class EnumTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Enum::class));
    }

    public function provideValues()
    {
        return [
            ['a', ['a', 'b'], true],
            ['b', ['a', 'b'], true],

            ['c', ['a', 'b'], false],
            [1, ['1', '2'], false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $allowedValues, $expected)
    {
        $type = new Enum($allowedValues);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
