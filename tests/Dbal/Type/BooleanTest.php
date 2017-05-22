<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Boolean;
use ORM\Test\TestCase;

class BooleanTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Boolean::class));
    }

    public function provideValues()
    {
        return [
            ['1', '0', true, true],
            ['1', '0', false, true],

            ['1', '0', '1', true],
            ['1', '0', '0', true],
            ['1', '0', 0, true],
            ['1', '0', 1, true],
            ['true', 'false', 'true', true],
            ['true', 'false', 'false', true],
            ['\'y\'', '\'n\'', 'y', true],
            ['\'y\'', '\'n\'', 'n', true],

            ['1', '0', 't', false],
            ['1', '0', 'f', false],
            ['true', 'false', '1', false],
            ['true', 'false', '0', false],
            ['true', 'false', 1, false],
            ['true', 'false', 0, false],
            ['\'y\'', '\'n\'', 'true', false],
            ['\'y\'', '\'n\'', 'false', false],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($true, $false, $value, $expected)
    {
        $type = new Boolean($true, $false);

        $result = $type->validate($value);

        self::assertSame($expected, $result);
    }
}
