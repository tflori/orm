<?php

namespace ORM\Test\EntityManager;

use Mockery as m;
use ORM\Entity;
use ORM\Relation\ManyToMany;
use ORM\Relation\OneToOne;
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
    public function repeatsTheProcessForNestedRelations()
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

    /** @test */
    public function correctlyCollectsAllRelatedObjectsToAnArray()
    {
        $user1 = m::mock(User::class, ['id' => 1]);
        $user2 = m::mock(User::class, ['id' => 2]);
        $articles = [
            m::mock(Article::class, ['id' => 1]),
            m::mock(Article::class, ['id' => 2]),
            m::mock(Article::class, ['id' => 3]),
        ];

        foreach ($articles as $i => $article) {
            $article->shouldReceive('hasLoaded')->with('writer')->once()->andReturnTrue();
            $article->shouldReceive('getRelated')->with('writer')->once()->andReturn($i%2 === 0 ? $user1 : $user2);
        }

        $user1->shouldReceive('hasLoaded')->with('contact')->once()->andReturn(true);
        $user2->shouldReceive('hasLoaded')->with('contact')->once()->andReturn(false);
        $user1->shouldReceive('getRelation')->with('contact')->once()
            ->andReturn($relation = m::mock(OneToOne::class));
        $relation->shouldReceive('eagerLoad')->with($this->em, $user1, $user2, $user1)->once();

        $this->em->eagerLoad('writer.contact', ...$articles);
    }
}
