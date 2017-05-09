<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Json;
use ORM\Test\TestCase;

class JsonTest extends TestCase
{
    public function provideTypes()
    {
        return [
            // postgres integer
            ['a', 'json', true],
            ['z', 'anything', false],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testIsType($name, $type, $expected)
    {
        $result = Json::isType($name, $type);

        self::assertSame($expected, $result);
    }
}
