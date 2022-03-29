<?php

namespace ORM\Test\Dbal\Mysql;

use Mockery as m;
use ORM\Dbal\Mysql;
use ORM\EM;
use ORM\Test\TestCase;

class UpdateJoinTest extends TestCase
{
    /** @var Pgsql */
    protected $dbal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbal = new Mysql($this->em);
    }

    /** @test */
    public function executesAnUpdateJoinStatement()
    {
        $this->pdo->shouldReceive('query')->with(
            "UPDATE examples JOIN names ON exampleId = examples.id SET \"foo\" = names.foo WHERE examples.id = 42"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->em->query('examples')
            ->join('names', 'exampleId = examples.id')
            ->where('examples.id', 42)
            ->update(['foo' => EM::raw('names.foo')]);
    }
}
