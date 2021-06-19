<?php

namespace ORM\Test\EntityManager;

use Mockery as m;
use ORM\Entity;
use ORM\Relation\ManyToMany;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;

class EagerLoadTest extends TestCase
{
    /** @test */
    public function getsTheRelationFromTheFirstObject()
    {
        $entities = [
            $first = m::mock(Article::class, ['id' => 1])->makePartial(),
            new Article(['id' => 2])
        ];

        $first->shouldReceive('getRelation')->with('categories')->once()
            ->andReturn($relation = m::mock(ManyToMany::class));
        $relation->shouldReceive('eagerLoad')->with($this->em, ...$entities)->once();

        $this->em->eagerLoad('categories', ...$entities);
    }

    /** @test */
    public function checksThatEveryObjectHasLoadedTheRelation()
    {
        $entities = [
            $first = m::mock(Article::class, ['id' => 1]),
            m::mock(Article::class, ['id' => 2]),
            m::mock(Article::class, ['id' => 3]),
        ];

        foreach ($entities as $mock) {
            $mock->shouldReceive('hasLoaded')->with('categories')->once()
                ->andReturn(true);
        }

        $first->shouldNotReceive('getRelation');

        $this->em->eagerLoad('categories', ...$entities);
    }

    /** @test */
    public function loadsTheRelationWhenOneObjectHasNotLoadedTheRelation()
    {
        $entities = [
            $first = m::mock(Article::class, ['id' => 1]),
            $second = m::mock(Article::class, ['id' => 1]),
        ];

        $first->shouldReceive('hasLoaded')->with('categories')->once()->andReturn(false);
        $second->shouldNotReceive('hasLoaded');
        $first->shouldReceive('getRelation')->with('categories')->once()
            ->andReturn($relation = m::mock(ManyToMany::class));
        $relation->shouldReceive('eagerLoad')->with($this->em, ...$entities)->once();

        $this->em->eagerLoad('categories', ...$entities);
    }

    /** @test */
    public function repeatsTheProcessForDeeperRelations()
    {
        $user = m::mock(User::class, ['id' => 1]);
        $articles = [
            $first = m::mock(Article::class, ['id' => 1]),
            $second = m::mock(Article::class, ['id' => 2]),
            m::mock(Article::class, ['id' => 3]),
        ];

        $user->shouldReceive('hasLoaded')->with('articles')->once()->andReturn(true);
        $user->shouldReceive('getRelated')->with('articles')->once()->andReturn($articles);

        $first->shouldReceive('hasLoaded')->with('categories')->once()->andReturn(false);
        $second->shouldNotReceive('hasLoaded');
        $first->shouldReceive('getRelation')->with('categories')->once()
            ->andReturn($relation = m::mock(ManyToMany::class));
        $relation->shouldReceive('eagerLoad')->with($this->em, ...$articles)->once();

        $this->em->eagerLoad('articles.categories', $user);
    }
}
