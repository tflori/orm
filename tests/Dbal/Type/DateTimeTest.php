<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\DateTime;
use ORM\Test\TestCase;

class DateTimeTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'date', true],
            ['b', 'datetime', true],
            ['c', 'timestamp', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = DateTime::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
