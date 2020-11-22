<?php

namespace ORM\Test\Dbal\Other;

use Mockery as m;
use ORM\Dbal\Other;
use ORM\Test\TestCase;

class UpdateTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->dbal = new Other($this->em);
    }

    /** @test */
    public function executesAnUpdateStatement()
    {
        $this->pdo->shouldReceive('query')->with(
            "UPDATE \"examples\" SET \"foo\" = 'bar' WHERE \"id\" = 42"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->dbal->update('examples', ['id' => 42], ['foo' => 'bar']);
    }

    /** @test */
    public function updatesEscapedValues()
    {
        $this->pdo->shouldReceive('query')->with(
            "UPDATE \"examples\" SET \"col1\" = 'hempel\\'s sofa',\"col2\" = 23 WHERE \"id\" = 42"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->dbal->update('examples', ['id' => 42], ['col1' => 'hempel\'s sofa', 'col2' => 23]);
    }
}
