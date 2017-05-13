<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\VarChar;
use ORM\Test\TestCase;

class VarCharTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'varchar(20)', true],
            ['a', 'VARCHAR(20)', true],
            ['b', 'character varying', true],
            ['c', 'char', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = VarChar::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
