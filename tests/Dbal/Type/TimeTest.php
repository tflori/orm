<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Time;
use ORM\Test\TestCase;

class TimeTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Time::class));
    }

    public function provideValues()
    {
        $dt = \DateTime::createFromFormat('U.u', microtime(true))
            ->setTimezone(new \DateTimeZone('UTC'));

        return [
            [$dt->format('H:i:s'), true],
            [$dt->format('H:i:s.u\Z'), true],

            ['NOW()', false],
            ['5pm', false],
            [$dt, false],
            [$dt->format('c'), false],
            [$dt->format('r'), false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Time(3);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
