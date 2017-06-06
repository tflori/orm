<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
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

            ['c', ['a', 'b'], '\'c\' is not allowed by this set'],
            ['a,c', ['a', 'b'], '\'c\' is not allowed by this set'],
            [1, ['1', '2'], 'Only string values are allowed for set'],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $allowedValues, $expected)
    {
        $type = new Set($allowedValues);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
