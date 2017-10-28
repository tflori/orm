<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\Enum;
use ORM\Test\TestCase;

class EnumTest extends TestCase
{
    /** @test */
    public function exists()
    {
        self::assertTrue(class_exists(Enum::class));
    }

    public function provideValues()
    {
        return [
            ['a', ['a', 'b'], true],
            ['b', ['a', 'b'], true],

            ['c', ['a', 'b'], '\'c\' is not allowed by this enum'],
            [1, ['1', '2'], 'Only string values are allowed for enum'],
        ];
    }

    /** @dataProvider provideValues
     * @test */
    public function validate($value, $allowedValues, $expected)
    {
        $type = new Enum($allowedValues);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
