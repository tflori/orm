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
            ['int(11)', Type\Integer::class],
            ['int(8)', Type\Integer::class],
            ['tinyint(1)', Type\Integer::class],
            ['tinyint(3)', Type\Integer::class],
            ['smallint(5)', Type\Integer::class],
            ['mediumint(8)', Type\Integer::class],
            ['bigint(20)', Type\Integer::class],
            ['bigint(20) unsigned', Type\Integer::class],

            ['decimal(5,2)', Type\Double::class],
            ['float', Type\Double::class],
            ['double', Type\Double::class],

            ['varchar(200)', Type\VarChar::class],
            ['char(5)', Type\VarChar::class],

            ['text', Type\Text::class],
            ['tinytext', Type\Text::class],
            ['mediumtext', Type\Text::class],
            ['longtext', Type\Text::class],

            ['datetime', Type\DateTime::class],
            ['date', Type\DateTime::class],
            ['timestamp', Type\DateTime::class],

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
        $this->pdo->shouldReceive('query')->with('DESCRIBE "db"."table"')->once()
            ->andReturn($statement);
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
        self::assertInstanceOf($class, $cols[0]->getType());
    }

    public function provideColumnData()
    {
        return [
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'NO', 'Key' => '', 'Default' => '0', 'Extra' => ''],
                'getName', 'a'
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
                'isNullable', false
            ],
            [
                ['Field' => 'a', 'Type' => 'int(11)', 'Null' => 'YES', 'Key' => '', 'Default' => null, 'Extra' => ''],
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
        $this->pdo->shouldReceive('query')->with('DESCRIBE "db"."table"')->once()
            ->andReturn($statement);
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)->twice()->andReturn($data, false);

        $cols = $this->dbal->describe('db.table');

        self::assertSame(1, count($cols));
        self::assertSame($expected, call_user_func([$cols[0], $method]));
    }
}
