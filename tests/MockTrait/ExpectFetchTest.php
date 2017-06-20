<?php

namespace ORM\Test\MockTrait;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;

class ExpectFetchTest extends MockeryTestCase
{
    use MockTrait;

    /** @var EntityManager|MockInterface */
    protected $em;

    protected function setUp()
    {
        $this->em = $this->ormInitMock();
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    public function testReturnsFetcher()
    {
        $fetcher = $this->ormExpectFetch(Article::class);

        self::assertInstanceOf(EntityFetcher::class, $fetcher);

        \Mockery::resetContainer();
    }

    public function testMocksFetch()
    {
        $fetcher = $this->ormExpectFetch(Article::class);

        self::assertSame($fetcher, $this->em->fetch(Article::class));
    }

    public function testReturnsNull()
    {
        $this->ormExpectFetch(Article::class);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->one();

        self::assertNull($result);
    }

    public function testReturnsEntities()
    {
        $articles = [new Article(), new Article()];
        $this->ormExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->all();

        self::assertSame($articles, $result);
    }

    public function testReturnsCount()
    {
        $articles = [new Article(), new Article()];
        $this->ormExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->count();

        self::assertSame(2, $result);
    }

    public function testExpectFetchOnEntity()
    {
        $categories = [new Category(), new Category()];
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectFetch(Category::class, $categories);

        $fetcher = $article->fetch('categories');
        $result = $fetcher->all();

        self::assertSame($categories, $result);
    }
}
