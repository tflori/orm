<?php

namespace ORM\Test\Testing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\Exception;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestEntityManager;
use ORM\Testing\MocksEntityManager;

class CreateMockedEntityTest extends MockeryTestCase
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
        m::close();
    }

    /** @test */
    public function throwsWithoutInitializing()
    {
        TestEntityManager::resetStaticsForTest();

        self::expectException(Exception::class);
        self::expectExceptionMessage('No entity manager initialized');

        $this->ormCreateMockedEntity(Article::class);
    }

    /** @test */
    public function throwsIfClassIsNotAnEntity()
    {
        self::expectException(Exception\NoEntity::class);
        self::expectExceptionMessage(' is not a subclass of Entity');

        $this->ormCreateMockedEntity(self::class);
    }

    /** @test */
    public function returnsTheEntity()
    {
        $article = $this->ormCreateMockedEntity(Article::class);

        self::assertInstanceOf(Article::class, $article);
    }

    /** @test */
    public function setsTheData()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);

        self::assertSame(42, $article->id);
        self::assertSame('Hello World!', $article->title);
    }

    /** @test */
    public function allowsUsingAttributeKeys()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'wordCount' => 3231]);

        self::assertSame(['id' => 42, 'word_count' => 3231], $article->getData());
        self::assertSame(3231, $article->wordCount);
    }

    /** @test */
    public function updateTheMock()
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

    /** @test */
    public function allowsValidationInSetAttribute()
    {
        $entity = $this->ormCreateMockedEntity(Category::class, ['name' => 'Foo Bar']);

        $entity->name = 'This could be limited to 20 chars but here it is not...';
    }
}
