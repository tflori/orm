<?php

namespace ORM\Test\Dbal\Sqlite;

use Mockery as m;
use ORM\Dbal\Sqlite;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\TestCase;

class InsertTest extends TestCase
{
    /** @var Sqlite */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Sqlite($this->em);
    }

    /** @test */
    public function buildsValidQueryForCompositeKeys()
    {
        $entity = new ContactPhone(['id' => 1, 'name' => 'mobile', 'number' => '+1 555 123']);

        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/INSERT INTO .* \("id","name","number"\) VALUES \(.*\)/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE \("id","name"\) IN \(VALUES (\(.*\))(,\(.*\))*\)/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->once()->with(\PDO::FETCH_ASSOC, null, [])->andReturnTrue();
        $statement->shouldReceive('fetch')->with()
            ->times(2)->andReturn(
                ['id' => 1, 'name' => 'mobile', 'number' => '+1 555 123', 'created' => date('c')],
                false
            );

        $this->dbal->insertAndSync($entity);

        self::assertSame('+1 555 123', $entity->number);
        self::assertNotNull($entity->created);
    }
}
