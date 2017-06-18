<?php

namespace ORM\Test\MockTrait;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;

class ExpectFetchTest extends MockeryTestCase
{
    use MockTrait;

    /** @var EntityManager|MockInterface */
    protected $em;

    protected function setUp()
    {
        $this->em = $this->emInitMock();
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testReturnsFetcher()
    {
        $fetcher = $this->emExpectFetch(Article::class);

        self::assertInstanceOf(EntityFetcher::class, $fetcher);

        \Mockery::resetContainer();
    }

    public function testMocksFetch()
    {
        $fetcher = $this->emExpectFetch(Article::class);

        self::assertSame($fetcher, $this->em->fetch(Article::class));
    }

    public function testReturnsNull()
    {
        $this->emExpectFetch(Article::class);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->one();

        self::assertNull($result);
    }

    public function testReturnsEntities()
    {
        $articles = [new Article(), new Article()];
        $this->emExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->all();

        self::assertSame($articles, $result);
    }

    public function testReturnsCount()
    {
        $articles = [new Article(), new Article()];
        $this->emExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->count();

        self::assertSame(2, $result);
    }
}
