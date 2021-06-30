<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UndefinedRelation;
use ORM\Relation\OneToMany;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\Tag;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;

class OneToManyTest extends TestCase
{
    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = RelationExample::getRelation('contactPhones');

        self::assertInstanceOf(OneToMany::class, $result);
    }

    /** @test */
    public function fetchFiltersByForeignKeyAndReturnsFetcher()
    {
        $entity = new RelationExample(['id' => 42], $this->em);
        $fetcher = m::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->andReturn($fetcher);

        $result = $entity->fetch('contactPhones');

        self::assertSame($fetcher, $result);
    }

    /** @test */
    public function fetchThrowsWhenOpponentIsNotDefined()
    {
        $entity = new DamagedABBRVCase([], $this->em);

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation dmgd is not defined');

        $entity->fetch('undefined1tm');
    }

    /** @test */
    public function fetchThrowsWhenOpponentIsNotAnOwner()
    {
        $entity = new Snake_Ucfirst([], $this->em);

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('The opponent of a OneToMany relation has to be a Owner relation');

        $entity->fetch('invalid');
    }

    /** @test */
    public function fetchReturnsAllWithGetAll()
    {
        $entity = new RelationExample(['id' => 42], $this->em);
        $related = [new ContactPhone(), new ContactPhone()];
        $fetcher = m::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->passthru();
        $fetcher->shouldReceive('all')->with()->once()->andReturn($related);

        $result = $entity->fetch('contactPhones', true);

        self::assertSame($related, $result);
    }

    /** @test */
    public function fetchThrowsWhenKeyIsEmpty()
    {
        $entity = new RelationExample([], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('contactPhones');
    }

    /** @test */
    public function eagerLoadFetchesEntitiesOfOurKeys()
    {
        $users = [
            new User(['id' => 1, 'name' => 'john']),
            new User(['id' => 2, 'name' => 'jane']),
        ];

        $this->em->shouldReceive('fetch')->with(Article::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class)->makePartial());
        $fetcher->shouldReceive('whereIn')->with(['userId'], [['userId' => 1], ['userId' => 2]])->once()
            ->andReturnSelf();
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                new Article(['id' => 3, 'user_id' => 1, 'text' => 'Hello Jane!']),
                new Article(['id' => 4, 'user_id' => 2, 'text' => 'Hello John!']),
                new Article(['id' => 5, 'user_id' => 2, 'text' => 'Hello Carl!']),
            ]);

        $this->em->eagerLoad('articles', ...$users);
    }

    /** @test */
    public function eagerLoadAssignsTheEntities()
    {
        $users = [
            $user1 = new User(['id' => 1, 'name' => 'john']),
            $user2 = new User(['id' => 2, 'name' => 'jane']),
        ];

        $this->em->shouldReceive('fetch')->with(Article::class)
            ->andReturn($fetcher = m::mock(EntityFetcher::class, [$this->em, Article::class])->makePartial());
        $fetcher->shouldReceive('all')->with()
            ->andReturn([
                $article3 = new Article(['id' => 3, 'user_id' => 1, 'text' => 'Hello Jane!']),
                $article4 = new Article(['id' => 4, 'user_id' => 2, 'text' => 'Hello John!']),
                $article5 = new Article(['id' => 5, 'user_id' => 2, 'text' => 'Hello Carl!']),
            ]);

        $this->em->eagerLoad('articles', ...$users);

        self::assertSame([$article3], $user1->articles);
        self::assertSame([$article4, $article5], $user2->articles);
    }

    /** @test */
    public function eagerLoadMorphedRelationFetchesEntitiesWithMorphColumn()
    {
        $articles = [
            $article1 = new Article(['id' => 1, 'title' => 'a and b']),
            $article2 = new Article(['id' => 2, 'title' => 'only c']),
        ];

        $this->em->shouldReceive('fetch')->with(Tag::class)->once()
            ->andReturn($fetcher = m::mock(EntityFetcher::class)->makePartial());
        $fetcher->shouldReceive('where')->with('parentType', 'article')->once()
            ->andReturnSelf();
        $fetcher->shouldReceive('whereIn')->with(['parentId'], [['parentId' => 1],['parentId' => 2]])->once()
            ->andReturnSelf();
        $fetcher->shouldReceive('all')->with()->once()
            ->andReturn([
                $tag3 = new Tag(['id' => 3, 'name' => 'a', 'parent_type' => 'article', 'parent_id' => 1]),
                $tag4 = new Tag(['id' => 4, 'name' => 'b', 'parent_type' => 'article', 'parent_id' => 1]),
                $tag5 = new Tag(['id' => 5, 'name' => 'c', 'parent_type' => 'article', 'parent_id' => 2]),
            ]);

        $this->em->eagerLoad('tags', ...$articles);

        self::assertSame([$tag3, $tag4], $article1->tags);
        self::assertSame([$tag5], $article2->tags);
    }
}
