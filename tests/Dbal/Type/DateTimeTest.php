<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\DateTime;
use ORM\Test\TestCase;

class DateTimeTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(DateTime::class));
    }

    public function provideValues()
    {
        return [
            [new \DateTime(), true],
            ['2016-03-23 15:22:11', true],
            [date('c'), true],

            ['NOW()', false],
            ['23rd of June \'84 5pm', false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new DateTime(3);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
