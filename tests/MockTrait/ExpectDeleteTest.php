<?php

namespace ORM\Test\MockTrait;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;

class ExpectDeleteTest extends MockeryTestCase
{
    use MockTrait;

    /** @var EntityManager|m\MockInterface */
    protected $em;

    protected function setUp()
    {
        $this->em = $this->ormInitMock();
    }

    protected function tearDown()
    {
        m::close();
    }

    public function testAllowsDeleteOfEntity()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);

        $this->ormExpectDelete($article);

        $this->deleteArticle(42);
    }

    public function testAllowsDeleteOfClass()
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
