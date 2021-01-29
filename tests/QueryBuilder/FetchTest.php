<?php

namespace ORM\Test\QueryBuilder;

use Mockery as m;
use ORM\Test\TestCase;

class FetchTest extends TestCase
{
    /** @test */
    public function returnsOneRow()
    {
        $query = $this->em->query('table');
        $row = ['id' => 42, 'name' => 'foobar'];

        $this->pdo->shouldReceive('query')->with('SELECT * FROM table')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('fetch')->with()->once()->andReturn($row);

        $result = $query->one();

        self::assertSame($row, $result);
    }

    /** @test */
    public function returnsAllRows()
    {
        $query = $this->em->query('table');
        $row1 = ['id' => 42, 'name' => 'foobar'];
        $row2 = ['id' => 23, 'name' => 'foo bar'];

        $this->pdo->shouldReceive('query')->with('SELECT * FROM table')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('fetch')->with()->times(3)
            ->andReturn($row1, $row2, false);

        $result = $query->all();

        self::assertSame([$row1, $row2], $result);
    }
}
