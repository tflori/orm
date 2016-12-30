<?php

namespace ORM\Test\EntityManager;

use ORM\Exceptions\NotScalar;
use ORM\Test\TestCase;

class EscapeValueTest extends TestCase
{
    public function testOnlyConvertsScalarData()
    {
        $array = ['this','is','not','scalar'];

        self::expectException(NotScalar::class);
        self::expectExceptionMessage('$value has to be scalar data type. array given');

        $this->em->escapeValue($array);
    }

    public function provideScalarsWithoutStringAndBoolean()
    {
        return [
            [42, '42'],
            [3E3, '3000'],
            [-5E-8, '-5.0E-8'],
            [0.002, '0.002'],
            [42.1, '42.1'],
            [null, 'NULL'],
        ];
    }

    /**
     * @dataProvider provideScalarsWithoutStringAndBoolean
     */
    public function testConvertsScalar($value, $expected)
    {
        $result = $this->em->escapeValue($value);

        self::assertSame($expected, $result);
    }

    public function provideBooleanDefaults()
    {
        return [
            [true, 'mysql', '1'],
            [false, 'mysql', '0'],
            [true, 'sqlite', '1'],
            [false, 'sqlite', '0'],
            [true, 'pgsql', 'true'],
            [false, 'pgsql', 'false'],
        ];
    }

    /**
     * @dataProvider provideBooleanDefaults
     */
    public function testBooleanUseDefaults($value, $connectionType, $expected)
    {
        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn($connectionType);

        $result = $this->em->escapeValue($value);

        self::assertSame($expected, $result);
    }

    public function testStringsUseQuote()
    {
        $this->pdo->shouldReceive('quote')->once()->with('foobar')->andReturn('\'buzzword\'');

        $result = $this->em->escapeValue('foobar');

        self::assertSame('\'buzzword\'', $result);
    }
}
