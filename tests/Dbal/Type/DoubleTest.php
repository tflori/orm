<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Double;
use ORM\Test\TestCase;

class DoubleTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'decimal', true],
            ['c', 'float', true],
            ['d', 'double', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Double::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
