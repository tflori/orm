<?php

namespace ORM\Test\QueryBuilder;

use ORM\QueryBuilder\QueryBuilder;
use ORM\QueryBuilder\Parenthesis;
use ORM\Test\TestCase;

class JoinTest extends TestCase
{
    public function provideJoins()
    {
        return [
            ['join', 'JOIN'],
            ['leftJoin', 'LEFT JOIN'],
            ['rightJoin', 'RIGHT JOIN'],
            ['fullJoin', 'FULL JOIN']
        ];
    }

    /**
     * @dataProvider provideJoins
     */
    public function testJoinWithUsing($method, $sql)
    {
        $query = new QueryBuilder('foo');

        $result = call_user_func([$query, $method], 'bar', 'b_id', 'b');

        self::assertSame('SELECT * FROM foo ' . $sql . ' bar AS b USING (b_id)', $query->getQuery());
        self::assertSame($query, $result);
    }

    /**
     * @dataProvider provideJoins
     */
    public function testJoinWithExpression($method, $sql)
    {
        $query = new QueryBuilder('foo');

        $result = call_user_func([$query, $method], 'bar', 'bar_id = bar.id');

        self::assertSame('SELECT * FROM foo ' . $sql . ' bar ON bar_id = bar.id', $query->getQuery());
        self::assertSame($query, $result);
    }

    /**
     * @dataProvider provideJoins
     */
    public function testJoinWithExpressionAndArg($method, $sql)
    {
        $query = new QueryBuilder('foo');

        $result = call_user_func([$query, $method], 'bar', 'bar_id = bar.id AND bar.type = ?', '', 'pub');

        self::assertSame(
            'SELECT * FROM foo ' . $sql . ' bar ON bar_id = bar.id AND bar.type = \'pub\'',
            $query->getQuery()
        );
        self::assertSame($query, $result);
    }

    /**
     * @dataProvider provideJoins
     */
    public function testJoinWithExpressionAndArgs($method, $sql)
    {
        $query = new QueryBuilder('foo');

        $result = call_user_func([$query, $method], 'bar', 'bar.id = ? AND bar.type = ?', '', [42, 'pub']);

        self::assertSame(
            'SELECT * FROM foo ' . $sql . ' bar ON bar.id = 42 AND bar.type = \'pub\'',
            $query->getQuery()
        );
        self::assertSame($query, $result);
    }

    /**
     * @dataProvider provideJoins
     */
    public function testJoinWithParenthesis($method, $sql)
    {
        $query = new QueryBuilder('foo');

        /** @var Parenthesis $parenthesis */
        $parenthesis = call_user_func([$query, $method], 'bar', false);
        self::assertSame(Parenthesis::class, get_class($parenthesis));

        $parenthesis->where('bar_id = bar.id');
        $result = $parenthesis->close();

        self::assertSame('SELECT * FROM foo ' . $sql . ' bar ON (bar_id = bar.id)', $query->getQuery());
        self::assertSame($query, $result);
    }

    /**
     * @dataProvider provideJoins
     */
    public function testEmptyJoin($method, $sql)
    {
        $query = new QueryBuilder('foo');

        /** @var Parenthesis $parenthesis */
        $result = call_user_func([$query, $method], 'bar', true);


        self::assertSame('SELECT * FROM foo ' . $sql . ' bar', $query->getQuery());
        self::assertSame($query, $result);
    }
}
