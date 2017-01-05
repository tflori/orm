<?php

namespace ORM\Test\QueryBuilder;

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

    /**
     * @dataProvider provideTablesWithAliases
     */
    public function testReturnsBasicStatement($table, $alias, $result)
    {
        $query = new QueryBuilder($table, $alias);

        self::assertSame('SELECT * FROM ' . $result, $query->getQuery());
    }

    public function testSetColumns()
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

    public function testResetColumns()
    {
        $query = new QueryBuilder('foobar');
        $query->columns(['a']);

        $query->columns();

        self::assertSame('SELECT * FROM foobar', $query->getQuery());
    }

    public function testAddColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->column('asdf');

        self::assertSame('SELECT asdf FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testAddExpression()
    {
        $query = new QueryBuilder('foobar', '', $this->em);
        $query->column('IF(a = ?, 1, 0)', 'something');

        self::assertSame('SELECT IF(a = \'something\', 1, 0) FROM foobar', $query->getQuery());
    }

    public function testAddExpressionWithAlias()
    {
        $query = (new QueryBuilder('foobar'))->column('COUNT(1)', [], 'cnt');

        self::assertSame('SELECT COUNT(1) AS cnt FROM foobar', $query->getQuery());
    }

    public function testExpressionsUsingDefaultEntityManager()
    {
        $query = new QueryBuilder('foobar', '');

        $query->column('IF(a = ?, 1, 0)', 'something');

        self::assertSame('SELECT IF(a = \'something\', 1, 0) FROM foobar', $query->getQuery());
    }

    public function testLimit()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->limit(20);

        self::assertSame('SELECT * FROM foobar LIMIT 20', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testOffset()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->limit(20)->offset(20);

        self::assertSame('SELECT * FROM foobar LIMIT 20 OFFSET 20', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testGroupByColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->groupBy('col');

        self::assertSame('SELECT * FROM foobar GROUP BY col', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testGroupByExpression()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col > ?', 42);

        self::assertSame('SELECT * FROM foobar GROUP BY col > 42', $query->getQuery());
    }

    public function testGroupByMultiple()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col')
            ->groupBy('col2');

        self::assertSame('SELECT * FROM foobar GROUP BY col,col2', $query->getQuery());
    }

    public function testGroupByHaving()
    {
        $query = (new QueryBuilder('foobar'))
            ->groupBy('col HAVING MAX(col) > 0');

        self::assertSame('SELECT * FROM foobar GROUP BY col HAVING MAX(col) > 0', $query->getQuery());
    }

    public function testOrderByColumn()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->orderBy('col');

        self::assertSame('SELECT * FROM foobar ORDER BY col ASC', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testOrderByColumnDesc()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('col', QueryBuilder::DIRECTION_DESCENDING);

        self::assertSame('SELECT * FROM foobar ORDER BY col DESC', $query->getQuery());
    }

    public function testOrderByMultiple()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('col')
            ->orderBy('col2');

        self::assertSame('SELECT * FROM foobar ORDER BY col ASC,col2 ASC', $query->getQuery());
    }

    public function testOrderByExpression()
    {
        $query = (new QueryBuilder('foobar'))
            ->orderBy('IF(col > ?, 0, 1)', QueryBuilder::DIRECTION_ASCENDING, 42);

        self::assertSame('SELECT * FROM foobar ORDER BY IF(col > 42, 0, 1) ASC', $query->getQuery());
    }

    public function testCloseDoesNothing()
    {
        $query = new QueryBuilder('foobar');
        $result = $query->close();

        self::assertSame('SELECT * FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }

    public function testModifier()
    {
        $query = new QueryBuilder('foobar');

        $query->modifier("DISTINCT");
        $result = $query->modifier("SQL_NO_CACHE");

        self::assertSame('SELECT DISTINCT SQL_NO_CACHE * FROM foobar', $query->getQuery());
        self::assertSame($query, $result);
    }
}
