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
            ['+01234-01-01', false],
            ['-01234-01-01', false],

            ['NOW()', false],
            ['23rd of June \'84 5pm', false],
            [$dt->format('r'), false],
        ];
    }

    /**
     * @dataProvider provideValuesWithTime
     */
    public function testValidateWithTime($value, $expected)
    {
        $type = new DateTime(3, false);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
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

            ['NOW()', false],
            ['23rd of June \'84 5pm', false],
            [$dt->format('r'), false],
        ];
    }

    /**
     * @dataProvider provideValuesWithoutTime
     */
    public function testValidateWithoutTime($value, $expected)
    {
        $type = new DateTime(3, true);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
