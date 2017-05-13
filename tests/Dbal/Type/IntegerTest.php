<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Integer;
use ORM\Test\TestCase;

class IntegerTest extends TestCase
{
    public function provideTypes()
    {
        return [
            ['a', 'serial', true],
            ['b', 'bigserial', true],
            ['c', 'smallint', true],
            ['d', 'integer', true],
            ['e', 'bigint', true],
            ['f', 'tinyint', true],
            ['g', 'mediumint', true],
            ['h', 'int', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Integer::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
