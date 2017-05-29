<?php

namespace ORM\Test\Dbal\Pgsql;

use ORM\Dbal\Column;
use ORM\Dbal\Pgsql;
use ORM\Dbal\Type;
use ORM\Exception;
use ORM\Test\TestCase;

class DescribeTest extends TestCase
{
    /** @var Pgsql */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Pgsql($this->em);
    }

    public function testQueriesDescribeTable()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with(
            'SELECT ' .
              'column_name,column_default,data_type,is_nullable,character_maximum_length,datetime_precision ' .
            'FROM INFORMATION_SCHEMA.COLUMNS ' .
            'WHERE table_name = \'table\' AND table_schema = \'db\''
        )->once()->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->andReturn([]);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table db.table');

        $this->dbal->describe('db.table');
    }

    public function testQueriesPublicSchema()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with(
            'SELECT ' .
              'column_name,column_default,data_type,is_nullable,character_maximum_length,datetime_precision ' .
            'FROM INFORMATION_SCHEMA.COLUMNS ' .
            'WHERE table_name = \'table\' AND table_schema = \'public\''
        )->once()->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)->andReturn([]);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table table');

        $this->dbal->describe('table');
    }

    public function provideTypes()
    {
        return [
            ['integer', Type\Number::class],
            ['smallint', Type\Number::class],
            ['bigint', Type\Number::class],
            ['numeric', Type\Number::class],
            ['real', Type\Number::class],
            ['double precision', Type\Number::class],
            ['money', Type\Number::class],

            ['character varying', Type\VarChar::class],
            ['character', Type\VarChar::class],

            ['text', Type\Text::class],

            ['date', Type\DateTime::class],
            ['timestamp without time zone', Type\DateTime::class],
            ['timestamp with time zone', Type\DateTime::class],
            ['time without time zone', Type\Time::class],
            ['time with time zone', Type\Time::class],

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
                'column_name' => 'a',
                'column_default' => null,
                'data_type' => $type,
                'is_nullable' => 'NO',
                'character_maximum_length' => null,
                'datetime_precision' => null,
            ]
        ]);

        /** @var Column[] $cols */
        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertInstanceOf($class, $cols[0]->getType());
    }

    public function provideColumnData()
    {
        return [
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'NO', 'column_default' => '0',
                  'character_maximum_length' => null, 'datetime_precision' => null ],
                'name', 'a'
            ],
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'NO', 'column_default' => '0',
                  'character_maximum_length' => null, 'datetime_precision' => null ],
                'hasDefault', true
            ],
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'NO',
                  'column_default' => 'nextval(anysequence)', 'character_maximum_length' => null,
                  'datetime_precision' => null  ],
                'hasDefault', true
            ],
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'NO', 'column_default' => null,
                  'character_maximum_length' => null, 'datetime_precision' => null ],
                'hasDefault', false
            ],
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'NO', 'column_default' => '0',
                  'character_maximum_length' => null, 'datetime_precision' => null ],
                'nullable', false
            ],
            [
                [ 'column_name' => 'a', 'data_type' => 'int', 'is_nullable' => 'YES', 'column_default' => null,
                  'character_maximum_length' => null, 'datetime_precision' => null ],
                'nullable', true
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

        /** @var Column[] $cols */
        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        if (!is_callable([$cols[0], $method])) {
            self::assertSame($expected, $cols[0]->$method);
        } else {
            self::assertSame($expected, call_user_func([$cols[0], $method]));
        }
    }
}
