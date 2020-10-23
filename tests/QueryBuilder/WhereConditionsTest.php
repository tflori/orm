<?php

namespace ORM\Test\QueryBuilder;

use ORM\QueryBuilder\QueryBuilder;
use ORM\QueryBuilder\ParenthesisInterface;
use ORM\Test\TestCase;

class WhereConditionsTest extends TestCase
{
    public function provideWhereConditions()
    {
        return [
            [['name', '=', 'John Doe'], 'name = \'John Doe\''],
            [['name', 'John Doe'], 'name = \'John Doe\''],
            [['name', 'LIKE', '% Doe'], 'name LIKE \'% Doe\''],
            [['name', ['John Doe', 'Jane Doe']], 'name IN (\'John Doe\',\'Jane Doe\')'],
            [['name', ['John Doe']], 'name IN (\'John Doe\')'],
            [['name', 'not in', ['John Doe']], 'name NOT IN (\'John Doe\')'],
            [['firstname = lastname'], 'firstname = lastname'],
            [
                ['MATCH(name) AGAINST (? IN NATURAL LANGUAGE MODE)', 'doe'],
                'MATCH(name) AGAINST (\'doe\' IN NATURAL LANGUAGE MODE)'
            ],
            [['name IN (?, ?)', ['John Doe', 'Jane Doe']], 'name IN (\'John Doe\', \'Jane Doe\')']
        ];
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function whereConditions($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $result = call_user_func_array([$query, 'where'], $params);

        self::assertSame('SELECT * FROM foobar WHERE ' . $expected, $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function andWhereConditions($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $query->where('a = b');
        $result = call_user_func_array([$query, 'andWhere'], $params);

        self::assertSame('SELECT * FROM foobar WHERE a = b AND ' . $expected, $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function orWhereConditions($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $query->where('a = b');
        $result = call_user_func_array([$query, 'orWhere'], $params);

        self::assertSame('SELECT * FROM foobar WHERE a = b OR ' . $expected, $query->getQuery());
        self::assertSame($query, $result);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function parenthesis($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $parenthesis = $query->parenthesis();
        $pResult = call_user_func_array([$parenthesis, 'where'], $params);
        $result = $parenthesis->close();

        self::assertSame('SELECT * FROM foobar WHERE (' . $expected . ')', $query->getQuery());
        self::assertInstanceOf(ParenthesisInterface::class, $parenthesis);
        self::assertSame($parenthesis, $pResult);
        self::assertSame($result, $query);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function andParenthesis($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $query->where('a = b');
        $parenthesis = $query->andParenthesis();
        $pResult = call_user_func_array([$parenthesis, 'where'], $params);
        $result = $parenthesis->close();

        self::assertSame('SELECT * FROM foobar WHERE a = b AND (' . $expected . ')', $query->getQuery());
        self::assertInstanceOf(ParenthesisInterface::class, $parenthesis);
        self::assertSame($parenthesis, $pResult);
        self::assertSame($result, $query);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function innerAndParenthesis($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $parenthesis = $query->andParenthesis();
        $parenthesis->where('a = b');
        $pResult = call_user_func_array([$parenthesis, 'where'], $params);
        $result = $parenthesis->close();

        self::assertSame('SELECT * FROM foobar WHERE (a = b AND ' . $expected . ')', $query->getQuery());
        self::assertInstanceOf(ParenthesisInterface::class, $parenthesis);
        self::assertSame($parenthesis, $pResult);
        self::assertSame($result, $query);
    }

    /** @dataProvider provideWhereConditions
     * @test */
    public function orParenthesis($params, $expected)
    {
        $query = new QueryBuilder('foobar');
        $query->where('a = b');
        $parenthesis = $query->orParenthesis();
        $pResult = call_user_func_array([$parenthesis, 'where'], $params);
        $result = $parenthesis->close();

        self::assertSame('SELECT * FROM foobar WHERE a = b OR (' . $expected . ')', $query->getQuery());
        self::assertInstanceOf(ParenthesisInterface::class, $parenthesis);
        self::assertSame($parenthesis, $pResult);
        self::assertSame($result, $query);
    }

    /** @test */
    public function parenthesisInParenthesis()
    {
        $query = (new QueryBuilder('foobar'))
            ->parenthesis()
                ->parenthesis()
                    ->where('a', 1)
                    ->orWhere('b', 2)
                    ->close()
                ->parenthesis()
                    ->where('c', 3)
                    ->orWhere('d', 4)
                    ->close()
                ->close();

        self::assertSame('SELECT * FROM foobar WHERE ((a = 1 OR b = 2) AND (c = 3 OR d = 4))', $query->getQuery());
    }

    /** @test */
    public function allTogether()
    {
        self::assertSame(
            'SELECT * FROM foobar WHERE name = \'John Doe\' AND age > 42 AND (age NOT IN (42) OR name = \'Jane Doe\')',
            (new QueryBuilder('foobar'))
                ->where('name = ?', 'John Doe')
                ->andWhere('age', '>', 42)
                ->andParenthesis()
                    ->where('age', 'NOT IN', [42])
                    ->orWhere('name', 'Jane Doe')
                    ->close()
                ->getQuery()
        );
    }

    /** @test */
    public function staysQuestionmarksUntouched()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a IN (?, ?)', ['foo']);

        self::assertSame('SELECT * FROM foobar WHERE a IN (\'foo\', ?)', $query->getQuery());
    }

    /** @test */
    public function createsWhere0()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a', 0);

        self::assertSame('SELECT * FROM foobar WHERE a = 0', $query->getQuery());
    }

    /** @test */
    public function createsWhereEmpty()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a', '');

        self::assertSame('SELECT * FROM foobar WHERE a = \'\'', $query->getQuery());
    }

    /** @test */
    public function usesEqualityOperator()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a', null, '');

        self::assertSame('SELECT * FROM foobar WHERE a = \'\'', $query->getQuery());
    }

    /** @test */
    public function inEmptyArrayNeverMatches()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a', 'IN', []);

        self::assertSame('SELECT * FROM foobar WHERE 1 = 0', $query->getQuery());
    }

    /** @test */
    public function notInEmptyArrayAlwaysMatches()
    {
        $query = new QueryBuilder('foobar');

        $query->where('a', 'NOT IN', []);

        self::assertSame('SELECT * FROM foobar WHERE 1 = 1', $query->getQuery());
    }
}
