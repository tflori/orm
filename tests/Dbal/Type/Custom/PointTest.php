<?php

namespace ORM\Test\Dbal\Type\Custom;

use ORM\Dbal\Dbal;
use ORM\Dbal\Mysql;
use ORM\Dbal\Type\Integer;
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
        CustomDbal::resetRegisteredTypes();
    }

    public function testRegister()
    {
        Dbal::registerType(Point::class);

        self::assertSame([Point::class], CustomDbal::getRegisteredTypes());
    }

    public function testRegisterUniqueTypes()
    {
        Dbal::registerType(Point::class);
        Dbal::registerType(Point::class);

        self::assertSame([Point::class], CustomDbal::getRegisteredTypes());
    }

    public function testAllowsInstances()
    {
        $point = new Point();
        Dbal::registerType($point);

        self::assertSame([$point], CustomDbal::getRegisteredTypes());
    }

    public function testExecutesFromDefinitionForUnknownTypes()
    {
        $point = \Mockery::mock(Point::class);
        Dbal::registerType($point);
        $point->shouldReceive('fromDefinition')->once()->with([
            'data_type' => 'point',
            'column_name' => 'another_point',
            'is_nullable' => true,
            'column_default' => null,
            'character_maximum_length' => null,
            'datetime_precision' => null,
        ])->andReturn(null);

        $cols = $this->dbal->describe('db.table');
    }

    public function testExecutesFromDefinitionFromNextType()
    {
        Dbal::registerType(Integer::class);
        $point = \Mockery::mock(new Point());
        Dbal::registerType($point);

        $point->shouldReceive('fromDefinition')->once()->andReturn(null);

        $cols = $this->dbal->describe('db.table');
    }

    public function testExecutesLastRegisteredFirst()
    {
        $int = \Mockery::mock(new Integer());
        $point = \Mockery::mock(new Point());
        Dbal::registerType($int);
        Dbal::registerType($point);

        $point->shouldReceive('fromDefinition')->globally()->once()->ordered();
        $int->shouldReceive('fromDefinition')->globally()->once()->ordered();

        $cols = $this->dbal->describe('db.table');
    }

    public function testReturnsTheReturnedType()
    {
        $point = \Mockery::mock(new Point());
        Dbal::registerType($point);

        $point->shouldReceive('fromDefinition')->once()->andReturnSelf();

        $cols = $this->dbal->describe('db.table');

        self::assertSame($point, $cols[0]->getType());
    }
}
