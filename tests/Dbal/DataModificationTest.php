<?php

namespace ORM\Test\Dbal;

use Mockery as m;
use ORM\EM;
use ORM\Test\TestCase;
use PDOStatement;

class DataModificationTest extends TestCase
{
    /** @test */
    public function executesAnInsertStatement()
    {
        $this->pdo->shouldReceive('query')->with(
            "INSERT INTO \"examples\" (\"col1\",\"col2\") VALUES ('foo','bar')"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->dbal->insert('examples', ['col1' => 'foo', 'col2' => 'bar']);
    }

    /** @test */
    public function insertsEscapedValues()
    {
        $this->pdo->shouldReceive('query')->with(
            "INSERT INTO \"examples\" (\"col1\",\"col2\") VALUES ('hempel\\'s sofa','2020-11-12T08:42:00.000000Z')"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->dbal->insert('examples', ['col1' => 'hempel\'s sofa', 'col2' => new \DateTime('2020-11-12 08:42:00')]);
    }

    /** @test */
    public function insertsAllColumnsFromAllRows()
    {
        $this->pdo->shouldReceive('query')->with(
            "INSERT INTO \"examples\" (\"col1\",\"col2\") VALUES ('foo',NULL),(NULL,'bar')"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(2);

        $this->dbal->insert('examples', ['col1' => 'foo'], ['col2' => 'bar']);
    }

    /** @test */
    public function insertReturns0IfNoRowsAreGiven()
    {
        $result = $this->dbal->insert('examples');

        self::assertSame(0, $result);
    }

    /** @test */
    public function insertReturnsTheNumberOfAffectedRows()
    {
        $this->pdo->shouldReceive('query')->with(
            "INSERT INTO \"examples\" (\"col1\",\"col2\") VALUES ('foo',NULL),(NULL,'bar')"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(2);

        $result = $this->dbal->insert('examples', ['col1' => 'foo'], ['col2' => 'bar']);

        self::assertSame(2, $result);
    }
}
