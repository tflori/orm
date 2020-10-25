<?php

namespace ORM\Test\Entity;

use Mockery as m;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class ExistsTest extends TestCase
{
    /** @test */
    public function anEntityFromDatabaseExists()
    {
        $entity = new Article(['id' => 42, 'title' => 'Foobar'], $this->em, true);

        self::assertTrue($entity->exists());
    }

    /** @test */
    public function afterInsertAnEntityExists()
    {
        $entity = new Article();

        $this->em->shouldReceive('insert')->with($entity, true)
            ->once()->andReturn(true);

        $entity->save();

        self::assertTrue($entity->exists());
    }

    /** @test */
    public function afterSyncAnEntityExists()
    {
        $entity = new Article(['id' => 42]);

        $this->pdo->shouldReceive('query')->with('SELECT DISTINCT t0.* FROM "article" AS t0 WHERE "t0"."id" = 42')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)
            ->once()->andReturn(['id' => 42, 'title' => 'Foobar']);

        $this->em->sync($entity);

        self::assertTrue($entity->exists());
    }

    /** @test */
    public function afterRestoreAnEntityStillExists()
    {
        $serialized = serialize(new Article(['id' => 42], $this->em, true));

        $entity = unserialize($serialized);

        self::assertTrue($entity->exists());
    }
}
