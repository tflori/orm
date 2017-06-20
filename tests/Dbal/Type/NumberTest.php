<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\Number;
use ORM\Test\TestCase;

class NumberTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Number::class));
    }

    public function provideValues()
    {
        return [
            [42, true], // integer
            [6*7.0, true], // double
            ['42', true],
            ['12.01', true],
            ['-12E2', true],
            ['+12.002E2', true],
            ['-2E-5', true],

            ['E23', 'E23 is not numeric'],
            ['zweiundvierzig', 'zweiundvierzig is not numeric'],
            ['1,235.01', '1,235.01 is not numeric'],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Number();

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
