<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Text;
use ORM\Test\TestCase;

class TextTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Text::class));
    }

    public function provideValues()
    {
        return [
            ['unlimited string length', true],
            [(string)42, true],

            [42, false], // only string accepted
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Text();

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
