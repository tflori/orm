<?php

namespace ORM\Test\Dbal\Pgsql;

use Mockery as m;
use ORM\Dbal\Pgsql;
use ORM\EM;
use ORM\Exception;
use ORM\Test\TestCase;

class UpdateFromTest extends TestCase
{
    /** @var Pgsql */
    protected $dbal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbal = new Pgsql($this->em);
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

    /** @test */
    public function updateFromOnlyWorksWithInnerJoins()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Only inner joins with on clause are allowed in update from statements');


        $this->em->query('examples')
            ->leftJoin('names', 'exampleId = examples.id')
            ->where('examples.id', 42)
            ->update(['foo' => EM::raw('names.foo')]);
    }
}
