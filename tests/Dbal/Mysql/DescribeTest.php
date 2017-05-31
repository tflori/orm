<?php

namespace ORM\Test\Dbal\Mysql;

use ORM\Dbal\Mysql;
use ORM\Dbal\Type;
use ORM\Exception;
use ORM\Test\TestCase;

class DescribeTest extends TestCase
{
    /** @var Mysql */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Mysql($this->em);
    }

    public function testQueriesDescribeTable()
    {
        $this->pdo->shouldReceive('query')->with('DESCRIBE "db"."table"')->once()
            ->andThrow(
                \PDOException::class,
                'SQLSTATE[42S02]: Base table or view not found: 1146 Table \'db.table\' doesn\'t exist'
            );
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown table db.table');

        $this->dbal->describe('db.table');
    }

    public function provideTypes()
    {
        return [
            ['int(11)', Type\Number::class],
            ['int(8)', Type\Number::class],
            ['tinyint(1)', Type\Number::class],
            ['tinyint(3)', Type\Number::class],
            ['smallint(5)', Type\Number::class],
            ['mediumint(8)', Type\Number::class],
            ['bigint(20)', Type\Number::class],
            ['bigint(20) unsigned', Type\Number::class],
            ['decimal(5,2)', Type\Number::class],
            ['float', Type\Number::class],
            ['double', Type\Number::class],

            ['varchar(200)', Type\VarChar::class],
            ['char(5)', Type\VarChar::class],

            ['text', Type\Text::class],
            ['tinytext', Type\Text::class],
            ['mediumtext', Type\Text::class],
            ['longtext', Type\Text::class],

            ['datetime(3)', Type\DateTime::class],
            ['datetime', Type\DateTime::class],
            ['date', Type\DateTime::class],
            ['timestamp(3)', Type\DateTime::class],
            ['timestamp', Type\DateTime::class],

            ['time(3)', Type\Time::class],
            ['time', Type\Time::class],
            ['enum(\'a\',\'b\')', Type\Enum::class],
            ['set(\'a\',\'b\')', Type\Set::class],
            ['json', Type\Json::class],

            ['anything', Type\Text::class]
        ];
    }

    /**
     * @dataProvider provideTypes
     */
    public function testColumnTypes($type, $class)
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn([
            'Field' => 'a',
            'Type' => $type,
            'Null' => 'NO',
            'Key' => '',
            'Default' => null,
            'Extra' => ''
        ], false);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertInstanceOf($class, $cols[0]->type);
    }

    public function provideColumnData()
    {
        return [
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => ''],
                'name', 'a'
            ],
            [
                [ 'Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => '' ],
                'hasDefault', true
            ],
            [
                [ 'Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => null,
                  'Extra' => 'auto_increment' ],
                'hasDefault', true
            ],
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => null, 'Extra' => ''],
                'hasDefault', false
            ],
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => ''],
                'nullable', false
            ],
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'YES', 'Key' => '', 'Default' => null, 'Extra' => ''],
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
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn($data, false);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        if (!is_callable([$cols[0], $method])) {
            self::assertSame($expected, $cols[0]->$method);
        } else {
            self::assertSame($expected, call_user_func([$cols[0], $method]));
        }
    }

    public function provideColumnTypeData()
    {
        return [
            ['varchar(200)', 'getMaxLength', 200],
            ['char(5)', 'getMaxLength', 5],

            ['datetime(3)', 'getPrecision', 3],
            ['datetime', 'getPrecision', 0],
            ['date', 'getPrecision', 0],
            ['timestamp(3)', 'getPrecision', 3],
            ['timestamp', 'getPrecision', 0],
            ['time(3)', 'getPrecision', 3],
            ['time', 'getPrecision', 0],

            ['set(\'a\',\'b\')', 'getAllowedValues', ['a', 'b']],
            ['enum(\'a\',\'b\')', 'getAllowedValues', ['a', 'b']],
        ];
    }

    /**
     * @dataProvider provideColumnTypeData
     */
    public function testColumnTypeData($type, $getter, $expected)
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn([
            'Field' => 'a',
            'Type' => $type,
            'Null' => 'NO',
            'Key' => '',
            'Default' => null,
            'Extra' => ''
        ], false);

        $cols = $this->dbal->describe('db.table');

        $result = call_user_func([$cols[0]->getType(), $getter]);

        self::assertSame($expected, $result);
    }
}
