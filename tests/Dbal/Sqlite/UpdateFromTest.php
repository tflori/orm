<?php

namespace ORM\Test\Dbal\Sqlite;

use Mockery as m;
use ORM\Dbal\Sqlite;
use ORM\EM;
use ORM\Test\TestCase;

class UpdateFromTest extends TestCase
{
    /** @var Sqlite */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();
        $this->dbal = new Sqlite($this->em);
    }

    /** @test */
    public function executesAnUpdateFromStatement()
    {
        $this->pdo->shouldReceive('query')->with(
            "UPDATE examples SET \"foo\" = names.foo FROM names WHERE exampleId = examples.id AND examples.id = 42"
        )->once()->andReturn($statement = m::mock(PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $this->em->query('examples')
            ->join('names', 'exampleId = examples.id')
            ->where('examples.id', 42)
            ->update(['foo' => EM::raw('names.foo')]);
    }
}
