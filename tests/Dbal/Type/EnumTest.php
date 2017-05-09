<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Enum;
use ORM\Test\TestCase;

class EnumTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'enum', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Enum::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
