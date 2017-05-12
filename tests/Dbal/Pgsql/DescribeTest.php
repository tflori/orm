<?php

namespace ORM\Test\Dbal\Pgsql;

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
              'column_name,column_default,data_type,is_nullable,character_maximum_length ' .
            'FROM INFORMATION_SCHEMA.COLUMNS ' .
            'WHERE table_name = \'table\' AND table_schema = \'db\''
        )->once()->andReturn($statement);
        $statement->shouldReceive('rowCount')->andReturn(0);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table db.table');

        $this->dbal->describe('db.table');
    }

    public function testQueriesPublicSchema()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with(
            'SELECT ' .
              'column_name,column_default,data_type,is_nullable,character_maximum_length ' .
            'FROM INFORMATION_SCHEMA.COLUMNS ' .
            'WHERE table_name = \'table\' AND table_schema = \'public\''
        )->once()->andReturn($statement);
        $statement->shouldReceive('rowCount')->andReturn(0);
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table table');

        $this->dbal->describe('table');
    }

    public function provideTypes()
    {
        return [
            ['integer', Type\Integer::class],
            ['smallint', Type\Integer::class],
            ['bigint', Type\Integer::class],

            ['numeric', Type\Double::class],
            ['real', Type\Double::class],
            ['double precision', Type\Double::class],
            ['money', Type\Double::class],

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
        $this->pdo->shouldReceive('query')->with(
            'SELECT ' .
                'column_name,column_default,data_type,is_nullable,character_maximum_length ' .
            'FROM INFORMATION_SCHEMA.COLUMNS ' .
            'WHERE table_name = \'table\' AND table_schema = \'db\''
        )->once()->andReturn($statement);
        $statement->shouldReceive('rowCount')->andReturn(1);
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn([
            'column_name' => 'a',
            'column_default' => null,
            'data_type' => $type,
            'is_nullable' => 'NO',
            'character_maximum_length' => null,
        ], false);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertInstanceOf($class, $cols[0]->getType());
    }
//
//    public function provideColumnData()
//    {
//        return [
//            [
//                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => ''],
//                'getName', 'a'
//            ],
//            [
//                [ 'Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => '' ],
//                'hasDefault', true
//            ],
//            [
//                [ 'Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => null,
//                  'Extra' => 'auto_increment' ],
//                'hasDefault', true
//            ],
//            [
//                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => null, 'Extra' => ''],
//                'hasDefault', false
//            ],
//            [
//                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => ''],
//                'isNullable', false
//            ],
//            [
//                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'YES', 'Key' => '', 'Default' => null, 'Extra' => ''],
//                'isNullable', true
//            ],
//        ];
//    }
//
//    /**
//     * @dataProvider provideColumnData
//     */
//    public function testColumnData($data, $method, $expected)
//    {
//        $statement = \Mockery::mock(\PDOStatement::class);
//        $this->pdo->shouldReceive('query')->with('DESCRIBE "db"."table"')->once()
//            ->andReturn($statement);
//        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn($data, false);
//
//        $cols = $this->dbal->describe('db.table');
//
//        self::assertSame(1, count($cols));
//        self::assertSame($expected, call_user_func([$cols[0], $method]));
//    }
}
