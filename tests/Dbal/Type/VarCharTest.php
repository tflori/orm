<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\VarChar;
use ORM\Test\TestCase;

class VarCharTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(VarChar::class));
    }

    public function provideValues()
    {
        return [
            ['The answer is 42', 16, true],
            ['Short value', 16, true],
            ['unlimited string length', 0, true],
            ['utf8 chars like äöü', 19, true],
            [(string)42, 0, true],

            ['This value is too long', 21, '\'This value is too long\' is too long (max: 21)'],
            [42, 0, 'Only string values are allowed for varchar'], // only string accepted
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $maxLen, $expected)
    {
        $type = new VarChar($maxLen);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
