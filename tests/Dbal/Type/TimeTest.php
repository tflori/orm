<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
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

            ['NOW()', 'NOW() is not a valid time expression'],
            ['5pm', '5pm is not a valid time expression'],
            [$dt, 'DateTime is not allowed for time'],
            [$dt->format('c'), $dt->format('c') . ' is not a valid time expression'],
            [$dt->format('r'), $dt->format('r') . ' is not a valid time expression'],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Time(3);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
