<?php

namespace ORM\Test\MockTrait;

use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class CreateMockedEntityTest extends TestCase
{
    use MockTrait;

    protected $em;

    protected function setUp()
    {
        $this->em = $this->emInitMock();
    }

    public function testReturnsTheEntity()
    {
        $article = $this->emCreateMockedEntity(Article::class);

        self::assertInstanceOf(Article::class, $article);
    }

    public function testSetsTheData()
    {
        $article = $this->emCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);

        self::assertSame(42, $article->id);
        self::assertSame('Hello World!', $article->title);
    }
}
