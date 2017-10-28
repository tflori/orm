<?php

namespace ORM\Test\Dbal;

use ORM\Dbal;
use ORM\Exception\NotScalar;
use ORM\Test\TestCase;

class EscapeValueTest extends TestCase
{
    /** @var Dbal\Dbal */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Dbal\Other($this->em);
    }

    /** @test */
    public function onlyConvertsScalarData()
    {
        $array = ['this','is','not','scalar'];

        self::expectException(NotScalar::class);
        self::expectExceptionMessage('$value has to be scalar data type. array given');

        $this->dbal->escapeValue($array);
    }

    public function provideScalars()
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

    /** @dataProvider provideScalars
     * @test */
    public function convertsScalar($value, $expected)
    {
        $result = $this->dbal->escapeValue($value);

        self::assertSame($expected, $result);
    }

    public function provideBooleanDefaults()
    {
        return [
            [true, '1'],
            [false, '0'],
        ];
    }

    /** @dataProvider provideBooleanDefaults
     * @test */
    public function booleanUseDefaults($value, $expected)
    {
        $result = $this->dbal->escapeValue($value);

        self::assertSame($expected, $result);
    }

    /** @test */
    public function dateTime()
    {
        $dateTime = \DateTime::createFromFormat('U.u', '1496163695.123456');

        $result = $this->dbal->escapeValue($dateTime);

        self::assertSame('\'2017-05-30T17:01:35.123456Z\'', $result);
    }

    /** @test */
    public function stringsUseQuote()
    {
        $this->pdo->shouldReceive('quote')->once()->with('foobar')->andReturn('\'buzzword\'');

        $result = $this->dbal->escapeValue('foobar');

        self::assertSame('\'buzzword\'', $result);
    }

    /** @test */
    public function numericString()
    {
        $result = $this->dbal->escapeValue('1.1234567890123456');

        self::assertSame('\'1.1234567890123456\'', $result);
    }
}
