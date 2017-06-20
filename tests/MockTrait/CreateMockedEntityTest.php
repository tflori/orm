<?php

namespace ORM\Test\MockTrait;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;

class CreateMockedEntityTest extends MockeryTestCase
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

    public function testReturnsTheEntity()
    {
        $article = $this->ormCreateMockedEntity(Article::class);

        self::assertInstanceOf(Article::class, $article);
    }

    public function testSetsTheData()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);

        self::assertSame(42, $article->id);
        self::assertSame('Hello World!', $article->title);
    }

    public function testUpdateTheMock()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
        $article->shouldReceive('save')->once();

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);

        self::assertSame('Don`t Panic!', $article->title);
    }

    protected function updateEntity($class, $id, $data)
    {
        $entity = $this->em->fetch($class, $id);
        $entity->fill($data);
        $entity->save();
    }
}
