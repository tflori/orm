<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Boolean;
use ORM\Test\TestCase;

class BooleanTest extends TestCase
{
    public function provideTypes()
    {
        return [
            ['a', 'boolean', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Boolean::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
