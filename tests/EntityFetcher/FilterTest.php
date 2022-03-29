<?php

namespace ORM\Test\EntityFetcher;

use Mockery as m;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Exception\InvalidArgument;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\EntityFetcher\Examples\NotDeletedFilter;
use ORM\Test\TestCase;
use ORM\Test\TestEntityFetcher;

class FilterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        TestEntityFetcher::resetGlobalFiltersForTest();
    }

    /** @test */
    public function filtersAreAppliedBeforeBuildingTheQuery()
    {
        $fetcher = Article::query();
        $filter = m::mock(NotDeletedFilter::class)->makePartial();

        $fetcher->filter($filter);
        $filter->shouldNotHaveReceived('apply');

        $fetcher->getQuery();
        $filter->shouldHaveReceived('apply')->with($fetcher);
    }

    /** @test */
    public function filtersAreAppliedOnlyOnce()
    {
        $fetcher = Article::query();
        $filter = m::mock(NotDeletedFilter::class)->makePartial();
        $fetcher->filter($filter);

        $fetcher->getQuery();
        $fetcher->getQuery();

        $filter->shouldHaveReceived('apply')->once();
    }

    /** @test */
    public function filtersCanBeCallables()
    {
        $fetcher = Article::query();
        $filter = m::spy(function () {
        });

        $fetcher->filter($filter);
        $fetcher->getQuery();

        $filter->shouldHaveBeenCalled();
    }

    /** @test */
    public function filtersCanBeClassNames()
    {
        $fetcher = Article::query();

        $fetcher->filter(NotDeletedFilter::class);

        self::assertStringContainsString('"t0"."deleted" IS NULL', $fetcher->getQuery());
    }

    /** @test */
    public function throwsWhenReceivingOtherObjects()
    {
        $fetcher = Article::query();
        $any = new \DateTime();

        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('should be an instance of');

        $fetcher->filter($any);
    }

    /** @test */
    public function globalFiltersAreAutomaticallyApplied()
    {
        $filter = m::mock(NotDeletedFilter::class)->makePartial();
        EntityFetcher::registerGlobalFilter(Article::class, $filter);

        $fetcher = Article::query();
        $fetcher->getQuery();

        $filter->shouldHaveReceived('apply')->once();
    }

    /** @test */
    public function globalFiltersCanBeRegisteredThroughEntity()
    {
        $filter = m::mock(NotDeletedFilter::class)->makePartial();
        Article::registerGlobalFilter($filter);

        $fetcher = Article::query();
        $fetcher->getQuery();

        $filter->shouldHaveReceived('apply');
    }

    /** @test */
    public function globalFiltersCanBeRegisteredForTheSuperClass()
    {
        $filter = m::mock(NotDeletedFilter::class)->makePartial();
        Entity::registerGlobalFilter($filter);

        $fetcher = Article::query();
        $fetcher->getQuery();

        $filter->shouldHaveReceived('apply');
    }
    
    /** @test */
    public function filtersCanBeExcluded()
    {
        $filter = m::mock(NotDeletedFilter::class)->makePartial();
        Article::registerGlobalFilter($filter);

        $fetcher = Article::query();
        $fetcher->withoutFilter(NotDeletedFilter::class);
        $fetcher->getQuery();

        $filter->shouldNotHaveReceived('apply');
    }
}
