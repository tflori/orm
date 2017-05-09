<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Text;
use ORM\Test\TestCase;

class TextTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'text', true],
            ['b', 'tinytext', true],
            ['c', 'mediumtext', true],
            ['d', 'longtext', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Text::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
