<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Double;
use ORM\Test\TestCase;

class DoubleTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Double::class));
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

            ['E23', false],
            ['zweiundvierzig', false],
            ['1,235.01', false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Double();

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
