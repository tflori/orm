<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Type\Json;
use ORM\Test\TestCase;

class JsonTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Json::class));
    }

    public function provideValues()
    {
        return [
            [json_encode(['key' => 'value']), true],
            [json_encode(null), true],
            [json_encode(0), true],
            [json_encode(42), true],
            [json_encode(true), true],
            [json_encode(false), true],
            [json_encode(['a','b','c']), true],

            [42, 'Only string values are allowed for json'],
            // json allows only double quotes
            ['{\'key\':\'value\'}', '\'{\'key\':\'value\'}\' is not a valid JSON string'],
            // no valid json
            ['undefined', '\'undefined\' is not a valid JSON string'],
        ];
    }

    /**
     * @dataProvider provideValues
     */
    public function testValidate($value, $expected)
    {
        $type = new Json();

        $result = $type->validate($value);

        if ($expected !== true) {
            self::assertInstanceOf(Error::class, $result);
            self::assertSame($expected, $result->getMessage());
        } else {
            self::assertTrue($result);
        }
    }
}
