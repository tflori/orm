<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\DateTime;
use ORM\Test\TestCase;

class DateTimeTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(DateTime::class));
    }

    public function provideValuesWithTime()
    {
        $dt = \DateTime::createFromFormat('U.u', microtime(true))
            ->setTimezone(new \DateTimeZone('UTC'));

        return [
            [$dt, true],
            [$dt->format('Y-m-d H:i:s'), true],
            [$dt->format('c'), true],
            [$dt->format('Y-m-d\TH:i:s.u\Z'), true],

            // valid but no time
            ['+01234-01-01', '+01234-01-01 is not a valid date or date time expression'],
            ['-01234-01-01', '-01234-01-01 is not a valid date or date time expression'],

            ['NOW()', 'NOW() is not a valid date or date time expression'],
            ['23rd of June \'84 5pm', '23rd of June \'84 5pm is not a valid date or date time expression'],
            [$dt->format('r'), $dt->format('r') . ' is not a valid date or date time expression'],
        ];
    }

    /**
     * @dataProvider provideValuesWithTime
     */
    public function testValidateWithTime($value, $expected)
    {
        $type = new DateTime(3, false);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }

    public function provideValuesWithoutTime()
    {
        $dt = \DateTime::createFromFormat('U.u', microtime(true))
            ->setTimezone(new \DateTimeZone('UTC'));

        return [
            [$dt, true],
            ['+01234-01-01', true],
            ['-01234-01-01', true],
            ['1984-01-21', true],

            // valid but with time
            [$dt->format('Y-m-d H:i:s'), true],
            [$dt->format('c'), true],
            [$dt->format('Y-m-d\TH:i:s.u\Z'), true],

            ['NOW()', 'NOW() is not a valid date or date time expression'],
            ['23rd of June \'84 5pm', '23rd of June \'84 5pm is not a valid date or date time expression'],
            [$dt->format('r'), $dt->format('r') . ' is not a valid date or date time expression'],
        ];
    }

    /**
     * @dataProvider provideValuesWithoutTime
     */
    public function testValidateWithoutTime($value, $expected)
    {
        $type = new DateTime(3, true);

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
