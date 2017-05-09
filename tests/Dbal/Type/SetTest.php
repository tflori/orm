<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Set;
use ORM\Test\TestCase;

class SetTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'set', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Set::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
