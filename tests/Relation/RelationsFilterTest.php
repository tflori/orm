<?php

namespace ORM\Test\Relation;

use Mockery as m;
use ORM\EntityFetcher;
use ORM\Relation;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\User;
use ORM\Test\EntityFetcher\Examples\NotDeletedFilter;
use ORM\Test\TestCase;

class RelationsFilterTest extends TestCase
{
    /** @test */
    public function filtersCanBeDefinedForOneToOne()
    {
        $relation = Relation::createRelation(
            static::class,
            'test',
            ['one', Article::class, 'writer', [NotDeletedFilter::class]]
        );

        self::assertInstanceOf(Relation\OneToOne::class, $relation);
        self::assertCount(1, self::getProtectedProperty($relation, 'filters'));
        self::assertSame(NotDeletedFilter::class, self::getProtectedProperty($relation, 'filters')[0]);
    }

    /** @test */
    public function filtersAreAppliedToTheFetcherInOneToOneRelations()
    {
        $relation = Relation::createRelation(
            static::class,
            'test',
            ['one', Article::class, 'writer', [NotDeletedFilter::class]]
        );

        $this->em->shouldReceive('fetch')->with(Article::class)
            ->once()->andReturn($fetcher = m::mock(EntityFetcher::class, [
                $this->em,
                Article::class,
            ])->makePartial());
        $fetcher->shouldReceive('filter')->with(NotDeletedFilter::class)
            ->once();
        $fetcher->shouldReceive('one')->andReturn(null);

        $relation->fetch(new User(['id' => 23]), $this->em);
    }

    /** @test */
    public function filtersCanBeDefinedForOneToMany()
    {
        $relation = Relation::createRelation(
            static::class,
            'test',
            ['many', Article::class, 'writer', [NotDeletedFilter::class]]
        );

        self::assertInstanceOf(Relation\OneToMany::class, $relation);
        self::assertCount(1, self::getProtectedProperty($relation, 'filters'));
        self::assertSame(NotDeletedFilter::class, self::getProtectedProperty($relation, 'filters')[0]);
    }

    /** @test */
    public function filtersAreAppliedToTheFetcherInOneToManyRelations()
    {
        $relation = Relation::createRelation(
            static::class,
            'test',
            ['many', Article::class, 'writer', [NotDeletedFilter::class]]
        );

        $this->em->shouldReceive('fetch')->with(Article::class)
            ->once()->andReturn($fetcher = m::mock(EntityFetcher::class, [
                $this->em,
                Article::class,
            ])->makePartial());
        $fetcher->shouldReceive('filter')->with(NotDeletedFilter::class)
            ->once();
        $fetcher->shouldReceive('one')->andReturn(null);

        $relation->fetch(new User(['id' => 23]), $this->em);
    }

    /** @test */
    public function filtersCanBeDefinedForManyToMany()
    {
        $relation = Relation::createRelation(static::class, 'test', [
            'many',
            Article::class,
            ['id' => 'category_id'],
            'categories',
            'article_category',
            [NotDeletedFilter::class]
        ]);

        self::assertInstanceOf(Relation\ManyToMany::class, $relation);
        self::assertCount(1, self::getProtectedProperty($relation, 'filters'));
        self::assertSame(NotDeletedFilter::class, self::getProtectedProperty($relation, 'filters')[0]);
    }

    /** @test */
    public function filtersAreAppliedToTheFetcherInManyToManyRelations()
    {
        $relation = Relation::createRelation(static::class, 'test', [
            'many',
            Article::class,
            ['id' => 'category_id'],
            'categories',
            'article_category',
            [NotDeletedFilter::class]
        ]);

        $this->em->shouldReceive('fetch')->with(Article::class)
            ->once()->andReturn($fetcher = m::mock(EntityFetcher::class, [
                $this->em,
                Article::class,
            ])->makePartial());
        $fetcher->shouldReceive('filter')->with(NotDeletedFilter::class)
            ->once();
        $fetcher->shouldReceive('one')->andReturn(null);

        $relation->fetch(new Category(['id' => 23]), $this->em);
    }

    /** @test */
    public static function filtersCanBeProvidedByObjects()
    {
        $filter = new NotDeletedFilter();
        $relation = Relation::createRelation(static::class, 'test', [
            'many',
            Article::class,
            ['id' => 'category_id'],
            'categories',
            'article_category',
            [$filter]
        ]);

        self::assertSame($filter, self::getProtectedProperty($relation, 'filters')[0]);
    }
}
