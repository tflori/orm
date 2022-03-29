<?php

namespace ORM\Test\Testing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Testing\MocksEntityManager;

class ExpectDeleteTest extends MockeryTestCase
{
    use MocksEntityManager;

    /** @var EntityManager|m\MockInterface */
    protected $em;

    protected function setUp(): void
    {
        $this->em = $this->ormInitMock();
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function allowsDeleteOfEntity()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);

        $this->ormExpectDelete($article);

        $this->deleteArticle(42);
    }

    /** @test */
    public function allowsDeleteOfClass()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);

        $this->ormExpectDelete(Article::class);

        $this->deleteArticle(42);
    }

    protected function deleteArticle($id)
    {
        $article = $this->em->fetch(Article::class, $id);
        $this->em->delete($article);
    }
}
