<?php

namespace ORM\Test\EntityFetcher;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;

class EagerLoadTest extends TestCase
{
    /** @test */
    public function eagerLoadsRelationsAfterExecution()
    {
        /** @var EntityFetcher|m\MockInterface $fetcher */
        $fetcher = m::mock(EntityFetcher::class, [$this->em, User::class])->makePartial();
        $fetcher->shouldReceive('one')->times(3)->andReturn(
            $user1 = new User(['id' => 1]),
            $user2 = new User(['id' => 2]),
            null
        );

        $this->em->shouldReceive('eagerLoad')->with('articles.categories', $user1, $user2)->once();

        $fetcher->with('articles.categories');
        $fetcher->all();
    }

    /** @test */
    public function eagerLoadsAllDefinedRelations()
    {
        /** @var EntityFetcher|m\MockInterface $fetcher */
        $fetcher = m::mock(EntityFetcher::class, [$this->em, User::class])->makePartial();
        $fetcher->shouldReceive('one')->times(3)->andReturn(
            $user1 = new User(['id' => 1]),
            $user2 = new User(['id' => 2]),
            null
        );

        $this->em->shouldReceive('eagerLoad')->with('articles', $user1, $user2)->once()->ordered();
        $this->em->shouldReceive('eagerLoad')->with('articles.categories', $user1, $user2)->once()->ordered();
        $this->em->shouldReceive('eagerLoad')->with('contact', $user1, $user2)->once()->ordered();

        $fetcher->with('articles', 'articles.categories');
        $fetcher->with('contact')->all();
    }
}
