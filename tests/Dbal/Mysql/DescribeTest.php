<?php

namespace ORM\Test\Dbal\Mysql;

use ORM\Dbal\Mysql;
use ORM\Dbal\Type\Double;
use ORM\Dbal\Type\Integer;
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
            ->andThrow(\PDOException::class, 'Table does not exist');
        self::expectException(\PDOException::class);
        self::expectExceptionMessage('Table does not exist');

        $this->dbal->describe('db.table');
    }

    public function provideTypes()
    {
        return [
            ['int(11)', Integer::class],
            ['int(8)', Integer::class],
            ['tinyint(1)', Integer::class],
            ['tinyint(3)', Integer::class],
            ['smallint(5)', Integer::class],
            ['mediumint(8)', Integer::class],
            ['bigint(20)', Integer::class],
            ['bigint(20) unsigned', Integer::class],

            ['decimal(5,2)', Double::class],
            ['float', Double::class],
            ['double', Double::class],
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
