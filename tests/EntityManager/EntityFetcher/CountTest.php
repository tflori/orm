<?php

namespace ORM\Test\EntityManager\EntityFetcher;

use Mockery\Mock;
use Mockery as m;
use ORM\EntityFetcher;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class CountTest extends TestCase
{
    /** @var Mock */
    protected $statement;

    protected function setUp()
    {
        parent::setUp();

        $this->statement = m::mock(\PDOStatement::class);

        $this->pdo->shouldReceive('query')->with(m::pattern('/^SELECT COUNT\(DISTINCT t0\.\*\) FROM ".*" AS t0/'))
            ->andReturn($this->statement)->byDefault();
        $this->statement->shouldReceive('fetchColumn')->with()
            ->andReturn(42)->byDefault();
    }

    /** @test */
    public function countReturnsInteger()
    {
        $fetcher = $this->em->fetch(DamagedABBRVCase::class);

        $result = $fetcher->count();

        self::assertInternalType('integer', $result);
    }

    /** @test */
    public function countExecutesQuery()
    {
        /** @var EntityFetcher $fetcher */
        $fetcher = $this->em->fetch(StudlyCaps::class);

        $this->pdo->shouldReceive('query')->with('SELECT COUNT(DISTINCT t0.*) FROM "studly_caps" AS t0')
            ->once()->andReturn($this->statement);
        $this->statement->shouldReceive('fetchColumn')->with()
            ->once()->andReturn(42);

        $fetcher->count();
    }

    /** @test */
    public function fetchesAfterCount()
    {
        /** @var EntityFetcher $fetcher */
        $fetcher = $this->em->fetch(StudlyCaps::class);
        // we change column and modifier here
        $fetcher->count();

        // if we not reset it will execute a count query again and the count query will not fail
        self::expectException(\PDOException::class);
        self::expectExceptionMessage('Query failed by default');

        $fetcher->one();
    }

    /** @test */
    public function convertsResultToInt()
    {
        /** @var EntityFetcher $fetcher */
        $fetcher = $this->em->fetch(StudlyCaps::class);
        $this->statement->shouldReceive('fetchColumn')->with()
            ->once()->andReturn('42');

        $result = $fetcher->count();

        self::assertSame(42, $result);
    }
}
