<?php

namespace ORM\Test\Testing;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Testing\MocksEntityManager;

class ExpectFetchTest extends MockeryTestCase
{
    use MocksEntityManager;

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

    /** @test */
    public function returnsFetcher()
    {
        $fetcher = $this->ormExpectFetch(Article::class);

        self::assertInstanceOf(EntityFetcher::class, $fetcher);

        \Mockery::resetContainer();
    }

    /** @test */
    public function mocksFetch()
    {
        $fetcher = $this->ormExpectFetch(Article::class);

        self::assertSame($fetcher, $this->em->fetch(Article::class));
    }

    /** @test */
    public function returnsNull()
    {
        $this->ormExpectFetch(Article::class);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->one();

        self::assertNull($result);
    }

    /** @test */
    public function returnsEntities()
    {
        $articles = [new Article(), new Article()];
        $this->ormExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->all();

        self::assertSame($articles, $result);
    }

    /** @test */
    public function returnsCount()
    {
        $articles = [new Article(), new Article()];
        $this->ormExpectFetch(Article::class, $articles);

        $fetcher = $this->em->fetch(Article::class);
        $result = $fetcher->count();

        self::assertSame(2, $result);
    }

    /** @test */
    public function expectFetchOnEntity()
    {
        $categories = [new Category(), new Category()];
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectFetch(Category::class, $categories);

        $fetcher = $article->fetch('categories');
        $result = $fetcher->all();

        self::assertSame($categories, $result);
    }
}
