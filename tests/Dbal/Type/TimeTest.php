<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Time;
use ORM\Test\TestCase;

class TimeTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'time', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Time::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
