<?php

namespace ORM\Test\QueryBuilder;

use Mockery as m;
use ORM\Dbal\Expression;
use ORM\DbConfig;
use ORM\EntityManager;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\TestCase;

class BasicTest extends TestCase
{
    public function provideTablesWithAliases()
    {
        return [
            ['foobar', 'foo', 'foobar AS foo'],
            ['foobar', '', 'foobar'],
            ['CaseSensitive', 'CS', 'CaseSensitive AS CS'],
            ['snake_lower', '', 'snake_lower'],
        ];
    }

    /** @dataProvider provideTablesWithAliases
     * @test */
    public function returnsBasicStatement($table, $alias, $result)
    {
        $query = new QueryBuilder($table, $alias);

        self::assertSame('SELECT * FROM ' . $result, $query->getQuery());
    }

    /** @test */
    public function setColumns()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->columns([
            'a',
            'b',
            'c'
        ]);

        self::assertSame('SELECT a,b,c FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function resetColumns()
    {
        $query = new QueryBuilder('foobar');
        $query->columns(['a']);

        $query->columns();

        self::assertSame('SELECT * FROM foobar', $query->getQuery());
    }

    /** @test */
    public function addColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->column('asdf');

        self::assertSame('SELECT asdf FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function addExpression()
    {
        $query = new QueryBuilder('foobar', '', $this->em);
        $query->column('IF(a = ?, 1, 0)', 'something');

        self::assertSame('SELECT IF(a = \'something\', 1, 0) FROM foobar', $query->getQuery());
    }

    /** @test */
    public function addExpressionWithAlias()
    {
        $query = (new QueryBuilder('foobar'))->column('COUNT(1)', [], 'cnt');

        self::assertSame('SELECT COUNT(1) AS cnt FROM foobar', $query->getQuery());
    }

    /** @test */
    public function expressionsUsingDefaultEntityManager()
    {
        $query = new QueryBuilder('foobar', '');

        $query->column('IF(a = ?, 1, 0)', 'something');

        self::assertSame('SELECT IF(a = \'something\', 1, 0) FROM foobar', $query->getQuery());
    }

    /** @test */
    public function limit()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->limit(20);

        self::assertSame('SELECT * FROM foobar LIMIT 20', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function offset()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->limit(20)->offset(20);

        self::assertSame('SELECT * FROM foobar LIMIT 20 OFFSET 20', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function groupByColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->groupBy('col');

        self::assertSame('SELECT * FROM foobar GROUP BY col', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function groupByExpression()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col > ?', 42);

        self::assertSame('SELECT * FROM foobar GROUP BY col > 42', $query->getQuery());
    }

    /** @test */
    public function groupByMultiple()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col')
            ->groupBy('col2');

        self::assertSame('SELECT * FROM foobar GROUP BY col,col2', $query->getQuery());
    }

    /** @test */
    public function groupByHaving()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col HAVING MAX(col) > 0');

        self::assertSame('SELECT * FROM foobar GROUP BY col HAVING MAX(col) > 0', $query->getQuery());
    }

    /** @test */
    public function orderByColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->orderBy('col');

        self::assertSame('SELECT * FROM foobar ORDER BY col ASC', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function orderByColumnDesc()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('col', QueryBuilder::DIRECTION_DESCENDING);

        self::assertSame('SELECT * FROM foobar ORDER BY col DESC', $query->getQuery());
    }

    /** @test */
    public function orderByMultiple()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('col')
            ->orderBy('col2');

        self::assertSame('SELECT * FROM foobar ORDER BY col ASC,col2 ASC', $query->getQuery());
    }

    /** @test */
    public function orderByExpression()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('IF(col > ?, 0, 1)', QueryBuilder::DIRECTION_ASCENDING, 42);

        self::assertSame('SELECT * FROM foobar ORDER BY IF(col > 42, 0, 1) ASC', $query->getQuery());
    }

    /** @test */
    public function closeDoesNothing()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->close();

        self::assertSame('SELECT * FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function modifier()
    {
        $query = new QueryBuilder('foobar');

        $query->modifier("DISTINCT");
        $result = $query->modifier("SQL_NO_CACHE");

        self::assertSame('SELECT DISTINCT SQL_NO_CACHE * FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @test */
    public function executesDbalUpdate()
    {
        $query = new QueryBuilder('foo', '', $this->em);
        $query->where('foo.id', 42);
        $query->join('bar', 'foo.barId = bar.id');

        $this->dbal->shouldReceive('update')->with(
            m::type(Expression::class),
            ['foo.id = 42'],
            ['col1' => 'value'],
            ['JOIN bar ON foo.barId = bar.id']
        )->once()->andReturn(1);

        $query->update(['col1' => 'value']);
    }

    /** @test */
    public function executesDbalDelete()
    {
        $query = new QueryBuilder('foo', '', $this->em);
        $query->where('foo.id', 42);
        $query->join('bar', 'foo.barId = bar.id');

        $this->dbal->shouldReceive('delete')->with(
            m::type(Expression::class),
            ['foo.id = 42']
        )->once()->andReturn(1);

        $query->delete();
    }

    /** @test */
    public function executesDbalInsert()
    {
        $query = new QueryBuilder('foo', '', $this->em);

        $this->dbal->shouldReceive('insert')->with(
            m::type(Expression::class),
            ['col1' => 'val1.1', 'col2' => 'val2.1'],
            ['col1' => 'val1.2', 'col2' => 'val2.2']
        )->once()->andReturn(2);

        $query->insert(
            ['col1' => 'val1.1', 'col2' => 'val2.1'],
            ['col1' => 'val1.2', 'col2' => 'val2.2']
        );
    }

    /** @test */
    public function throwsWithClassName()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $em = new EntityManager([
            EntityManager::OPT_CONNECTION => new DbConfig('sqlite', '/tmp/test.sqlite')
        ]);
        $query = $em->query('sqlite_master');

        if (PHP_VERSION_ID >= 80000) {
            self::expectException(\ArgumentCountError::class);
            self::expectExceptionMessage(
                'PDOStatement::setFetchMode() expects exactly 1 arguments for the fetch mode provided'
            );
        } else {
            self::expectException(\PDOException::class);
            self::expectExceptionMessage(
                'SQLSTATE[HY000]: General error: fetch mode doesn\'t allow any extra arguments'
            );
        }

        $query->setFetchMode(\PDO::FETCH_ASSOC, static::class);
    }

    /** @test */
    public function doesNotPassNull()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $em = new EntityManager([
            EntityManager::OPT_CONNECTION => new DbConfig('sqlite', '/tmp/test.sqlite')
        ]);
        $query = $em->query('sqlite_master');

        $query->setFetchMode(\PDO::FETCH_ASSOC, null);
        $this->assertTrue(true); // it would have thrown an exception
    }

    /** @test */
    public function passesColumnNumber0()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for this test');
        }

        $em = new EntityManager([
            EntityManager::OPT_CONNECTION => new DbConfig('sqlite', '/tmp/test.sqlite')
        ]);
        $query = $em->query('sqlite_master');

        $query->setFetchMode(\PDO::FETCH_COLUMN, 0);
        $this->assertTrue(true); // it would have thrown an exception
    }
}
