<?php

namespace ORM\Test\Dbal\Sqlite;

use ORM\Dbal\Sqlite;
use ORM\Dbal\Type;
use ORM\Exception;
use ORM\Test\TestCase;

class DescribeTest extends TestCase
{
    /** @var Sqlite */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Sqlite($this->em);
    }

    public function testQueriesDescribeTable()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('PRAGMA "db".table_info("table")')->once()
            ->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->once()->andReturn([]);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table table');

        $this->dbal->describe('db.table');
    }

    public function testQueriesMainSchema()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('PRAGMA table_info("table")')->once()
            ->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->andReturn([]);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table table');

        $this->dbal->describe('table');
    }

    public function provideColumnData()
    {
        return [
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '0', 'pk' => '0', 'dflt_value' => null],
                'getName', 'a'
            ],
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '0', 'pk' => '0', 'dflt_value' => '0'],
                'hasDefault', true
            ],
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '0', 'pk' => '1', 'dflt_value' => null],
                'hasDefault', true
            ],
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '0', 'pk' => '0', 'dflt_value' => null],
                'hasDefault', false
            ],
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '1', 'pk' => '0', 'dflt_value' => '0'],
                'isNullable', false
            ],
            [
                ['cid' => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '0', 'pk' => '0', 'dflt_value' => null],
                'isNullable', true
            ],
        ];
    }

    /**
     * @dataProvider provideColumnData
     */
    public function testColumnData($data, $method, $expected)
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->once()->andReturn([$data]);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertSame($expected, call_user_func([$cols[0], $method]));
    }

    public function testMultiplePrimaryKeyHasNoDefault()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->once()->andReturn([
            ['cid'  => 0, 'name' => 'a', 'type' => 'integer', 'notnull' => '1'
             , 'pk' => '1', 'dflt_value' => null],
            ['cid'  => 1, 'name' => 'b', 'type' => 'integer', 'notnull' => '1'
             , 'pk' => '2', 'dflt_value' => null],
        ]);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(2, count($cols));
        self::assertSame(false, $cols[0]->hasDefault());
        self::assertSame(false, $cols[1]->hasDefault());
    }

    public function provideTypes()
    {
        return [
            ['integer', Type\Integer::class],
            ['int', Type\Integer::class],
            ['INTEGER', Type\Integer::class],
            ['INT', Type\Integer::class],

            ['numeric', Type\Double::class],
            ['real', Type\Double::class],
            ['double', Type\Double::class],
            ['decimal', Type\Double::class],
            ['NUMERIC', Type\Double::class],
            ['REAL', Type\Double::class],
            ['DOUBLE', Type\Double::class],
            ['DECIMAL', Type\Double::class],

            ['varchar(20)', Type\VarChar::class],
            ['character(5)', Type\VarChar::class],
            ['VARCHAR(20)', Type\VarChar::class],
            ['CHARACTER(5)', Type\VarChar::class],

            ['text', Type\Text::class],

            ['date', Type\DateTime::class],
            ['datetime', Type\DateTime::class],
            ['time', Type\Time::class],

            ['json', Type\Json::class],
            ['boolean', Type\Boolean::class],

            ['anything', Type\Text::class],
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testColumnTypes($type, $class)
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->once()->andReturn([
            [
                'cid' => 0,
                'name' => 'a',
                'type' => $type,
                'notnull' => '1',
                'pk' => '0',
                'dflt_value' => null
            ]
        ]);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertInstanceOf($class, $cols[0]->getType());
    }
}
