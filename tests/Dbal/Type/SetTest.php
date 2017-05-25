<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Set;
use ORM\Test\TestCase;

class SetTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Set::class));
    }

    public function provideValues()
    {
        return [
            ['a', ['a', 'b'], true],
            ['b', ['a', 'b'], true],
            ['a,b', ['a', 'b'], true],
            ['b,a', ['a', 'b'], true],

            ['c', ['a', 'b'], false],
            ['a,c', ['a', 'b'], false],
            [1, ['1', '2'], false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $allowedValues, $expected)
    {
        $type = new Set($allowedValues);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
