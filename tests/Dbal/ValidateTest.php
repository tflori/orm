<?php

namespace ORM\Test\Dbal;

use Mockery\Mock;
use ORM\Dbal\Column;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NotValid;
use ORM\Dbal\Type\Number;
use ORM\Exception\UnknownColumn;
use ORM\Test\TestCase;
use ORM\Dbal\Table;

class ValidateTest extends TestCase
{
    /** @var Number|Mock */
    protected $type;

    /** @var Column|Mock */
    protected $column;

    protected function setUp()
    {
        parent::setUp();

        $this->type = \Mockery::mock(Number::class);
    }


    /** @test */
    public function throwsWhenColumnDoesNotExist()
    {
        $validator = new Table([]);

        self::expectException(UnknownColumn::class);
        self::expectExceptionMessage('Unknown column id');

        $validator->validate('id', 23);
    }

    /** @test */
    public function passesValidateFromCol()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => true
        ]])->makePartial();
        $table = new Table([$this->column]);
        $this->column->shouldReceive('validate')->with('value')->once()->andReturn('return');

        $result = $table->validate('colA', 'value');

        self::assertSame('return', $result);
    }

    /** @test */
    public function allowsNullValues()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => true
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);
        $this->type->shouldNotReceive('validate');

        $result = $this->column->validate(null);

        self::assertTrue($result);
    }

    /** @test */
    public function allowsNullWithDefault()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => 23,
            'is_nullable' => false
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);
        $this->type->shouldNotReceive('validate');

        $result = $this->column->validate(null);

        self::assertSame(true, $result);
    }

    /** @test */
    public function returnsNotNullable()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => false
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);
        $this->type->shouldNotReceive('validate');

        $result = $this->column->validate(null);

        self::assertInstanceOf(\ORM\Dbal\Error\NotNullable::class, $result);
    }

    /** @test */
    public function validatesUsingType()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => false
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);

        $this->type->shouldReceive('validate')->with(true)->once()->andReturn(true);

        $result = $this->column->validate(true);

        self::assertSame(true, $result);
    }

    /** @test */
    public function returnsNotValid()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => false
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);

        $this->type->shouldReceive('validate')->with(true)->once()->andReturn(
            new Error()
        );

        $result = $this->column->validate('y');

        self::assertInstanceOf(\ORM\Dbal\Error\NotValid::class, $result);
    }

    /** @test */
    public function returnsNotValidOnFalse()
    {
        $this->column = \Mockery::mock(Column::class, [$this->dbal, [
            'column_name' => 'colA',
            'column_default' => null,
            'is_nullable' => false
        ]])->makePartial();
        $this->column->shouldReceive('getType')->andReturn($this->type);

        $this->type->shouldReceive('validate')->with(true)->once()->andReturn(false);

        $result = $this->column->validate('y');

        self::assertInstanceOf(\ORM\Dbal\Error\NotValid::class, $result);
        self::assertSame('UNKNOWN', $result->getPrevious()->getCode());
    }
}
