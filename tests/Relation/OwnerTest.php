<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Relation\Owner;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;

class OwnerTest extends TestCase
{
    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = RelationExample::getRelation('dmgd');

        self::assertInstanceOf(Owner::class, $result);
    }

    /** @test */
    public function fetchFetchesWithPrimaryKeyFor1T1Owner()
    {
        $entity = new RelationExample(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd');

        self::assertSame($related, $result);
    }

    /** @test */
    public function fetchReturnsNullWhenReferenceIsEmpty()
    {
        $entity = new RelationExample([], $this->em);

        $result = $entity->fetch('dmgd');

        self::assertNull($result);
    }

    /** @test */
    public function fetchAllReturnsTheEntity()
    {
        $entity = new RelationExample(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd', true);

        self::assertSame($related, $result);
    }

    /** @test */
    public function eagerLoadFetchesEntitiesByForeignKey()
    {
        $articles = [
            new Article(['id' => 3, 'user_id' => 1, 'text' => 'Hello Jane!']),
            new Article(['id' => 4, 'user_id' => 2, 'text' => 'Hello John!']),
            new Article(['id' => 5, 'user_id' => 2, 'text' => 'Hello Carl!']),
        ];

        $this->em->shouldReceive('fetch')->with(User::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class)->makePartial());
        $fetcher->shouldReceive('whereIn')->with(['id'], [['id' => 1],['id' => 2]])->once()
            ->andReturnSelf();
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                new User(['id' => 1, 'name' => 'john']),
                new User(['id' => 2, 'name' => 'jane']),
            ]);

        $this->em->eagerLoad('writer', ...$articles);
    }

    /** @test */
    public function eagerLoadAssignsForeignObjects()
    {
        $articles = [
            new Article(['id' => 3, 'user_id' => 1, 'text' => 'Hello Jane!']),
            new Article(['id' => 4, 'user_id' => 2, 'text' => 'Hello John!']),
            new Article(['id' => 5, 'user_id' => 2, 'text' => 'Hello Carl!']),
        ];

        $this->em->shouldReceive('fetch')->with(User::class)
            ->andReturn($fetcher = m::mock(EntityFetcher::class, [$this->em, User::class])->makePartial());
        $fetcher->shouldReceive('all')->with()->andReturn([
            $user1 = new User(['id' => 1, 'name' => 'john']),
            $user2 = new User(['id' => 2, 'name' => 'jane']),
        ]);

        $this->em->eagerLoad('writer', ...$articles);

        self::assertSame($user1, $articles[0]->writer);
        self::assertSame($user2, $articles[1]->writer);
        self::assertSame($user2, $articles[2]->writer);
    }
}
