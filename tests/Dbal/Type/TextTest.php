<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\Text;
use ORM\Test\TestCase;

class TextTest extends TestCase
{
    /** @test */
    public function exists()
    {
        self::assertTrue(class_exists(Text::class));
    }

    public function provideValues()
    {
        return [
            ['unlimited string length', true],
            [(string)42, true],

            [42, 'Only string values are allowed for text'], // only string accepted
        ];
    }

    /** @dataProvider provideValues
     * @test */
    public function validate($value, $expected)
    {
        $type = new Text();

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
