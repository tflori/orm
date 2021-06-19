<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Exception\UndefinedRelation;
use ORM\Relation\OneToOne;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\User;
use ORM\Test\Entity\Examples\UserContact;
use ORM\Test\TestCase;

class OneToOneTest extends TestCase
{
    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = DamagedABBRVCase::getRelation('relation');

        self::assertInstanceOf(OneToOne::class, $result);
    }

    /** @test */
    public function fetchFiltersByForeignKeyAndReturnsFirst()
    {
        $entity = new DamagedABBRVCase(['id' => 42], $this->em);
        $related = new RelationExample();
        $fetcher = m::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(RelationExample::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('dmgdId', 42)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('one')->with()->once()->andReturn($related);

        $result = $entity->fetch('relation');

        self::assertSame($related, $result);
    }

    /** @test */
    public function fetchThrowsWhenOpponentIsNotDefined()
    {
        $entity = new DamagedABBRVCase([], $this->em);

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation dmgd is not defined');

        $entity->fetch('undefined1t1');
    }

    /** @test */
    public function eagerLoaderLoadsEntitiesReferencingTheseObjects()
    {
        $users = [
            new User(['id' => 1, 'name' => 'john']),
            new User(['id' => 2, 'name' => 'jane']),
        ];

        $this->em->shouldReceive('fetch')->with(UserContact::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class)->makePartial());
        $fetcher->shouldReceive('whereIn')->with(['userId'], [['userId' => 1], ['userId' => 2]])->once()
            ->andReturnSelf();
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                new UserContact(['id' => 3, 'user_id' => 1, 'phone' => '+1555123456']),
                new UserContact(['id' => 4, 'user_id' => 2, 'phone' => '+1555234567']),
            ]);

        $this->em->eagerLoad('contact', ...$users);
    }

    /** @test */
    public function eagerLoadAssignsOnlyTheFirstObjectForTheRelation()
    {
        $users = [
            $user1 = new User(['id' => 1, 'name' => 'john']),
            $user2 = new User(['id' => 2, 'name' => 'jane']),
        ];

        $this->em->shouldReceive('fetch')->with(UserContact::class)
            ->andReturn($fetcher = m::mock(EntityFetcher::class, [$this->em, UserContact::class])->makePartial());
        // In theory you should make user_id unique because it is a one-to-one relation - then the third entity can not
        // exist. Anyway in this test we want to force this error to see what happens.
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                $contact3 = new UserContact(['id' => 3, 'user_id' => 1, 'phone' => '+1555123456']),
                $contact4 = new UserContact(['id' => 4, 'user_id' => 2, 'phone' => '+1555234567']),
                $contact5 = new UserContact(['id' => 5, 'user_id' => 2, 'phone' => '+1555345678']),
            ]);

        $this->em->eagerLoad('contact', ...$users);

        self::assertSame($contact3, $user1->contact);
        self::assertSame($contact4, $user2->contact);
    }
}
