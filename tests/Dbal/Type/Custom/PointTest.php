<?php

namespace ORM\Test\Dbal\Type\Custom;

use ORM\Dbal\Column;
use ORM\Dbal\Dbal;
use ORM\Dbal\Mysql;
use ORM\Dbal\Type\Number;
use ORM\Test\TestCase;

class PointTest extends TestCase
{
    /** @var Mysql */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Mysql($this->em);

        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->andReturn([
            'Field' => 'another_point',
            'Type' => 'point',
            'Null' => 'YES',
            'Key' => '',
            'Default' => null,
            'Extra' => ''
        ], false);
    }

    public function tearDown()
    {
        parent::tearDown();
        CustomColumn::resetRegisteredTypes();
    }

    public function testRegister()
    {
        Column::registerType(Point::class);

        self::assertSame([Point::class], CustomColumn::getRegisteredTypes());
    }

    public function testRegisterUniqueTypes()
    {
        Column::registerType(Point::class);
        Column::registerType(Point::class);

        self::assertSame([Point::class], CustomColumn::getRegisteredTypes());
    }

    public function testAllowsInstances()
    {
        $point = new Point();
        Column::registerType($point);

        self::assertSame([$point], CustomColumn::getRegisteredTypes());
    }

    public function testExecutesFitsForUnknownTypes()
    {
        $point = \Mockery::mock(Point::class);
        Column::registerType($point);
        $point->shouldReceive('fits')->once()->with([
            'data_type' => 'point',
            'column_name' => 'another_point',
            'is_nullable' => true,
            'column_default' => null,
            'character_maximum_length' => null,
            'datetime_precision' => null,
        ])->andReturn(false);

        $cols = $this->dbal->describe('db.table');
        $cols[0]->getType();
    }

    public function testExecutesFitsFromNextType()
    {
        Column::registerType(Number::class);
        $point = \Mockery::mock(new Point());
        Column::registerType($point);

        $point->shouldReceive('fits')->once()->andReturn(null);

        $cols = $this->dbal->describe('db.table');
        $cols[0]->getType();
    }

    public function testExecutesLastRegisteredFirst()
    {
        $int = \Mockery::mock(new Number());
        $point = \Mockery::mock(new Point());
        Column::registerType($int);
        Column::registerType($point);

        $point->shouldReceive('fits')->globally()->once()->ordered()->andReturn(false);
        $int->shouldReceive('fits')->globally()->once()->ordered()->andReturn(false);

        $cols = $this->dbal->describe('db.table');
        $cols[0]->getType();
    }

    public function testReturnsTheReturnedType()
    {
        $point = \Mockery::mock(new Point());
        Column::registerType($point);

        $point->shouldReceive('fits')->once()->andReturn(true);
        $point->shouldReceive('factory')->once()->andReturnSelf();

        $cols = $this->dbal->describe('db.table');
        $type = $cols[0]->getType();

        self::assertSame($point, $type);
    }
}
