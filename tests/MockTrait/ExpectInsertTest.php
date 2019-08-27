<?php

namespace ORM\Test\MockTrait;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Testing\MocksEntityManager;

class ExpectInsertTest extends MockeryTestCase
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
    public function allowsInsertOfSpecifiedClass()
    {
        $this->ormExpectInsert(Article::class, ['id' => 42]);
        $article = new Article();

        $article->save();
    }

    /** @test */
    public function doesNotAllowInsertsOfOtherClasses()
    {
        $this->ormExpectInsert(Category::class);
        $article = new Article();

        self::expectException(\BadMethodCallException::class);
        self::expectExceptionMessage('but no expectations were specified');

        try {
            $article->save();
        } catch (\Exception $e) {
            \Mockery::resetContainer();
            throw $e;
        }
    }

    /** @test */
    public function setsDefaultData()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->ormExpectInsert(Article::class, $defaults);
        $article = new Article();

        $article->save();

        self::assertSame($defaults, $article->getData());
    }

    /** @test */
    public function doesNotOverwriteCurrentData()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->ormExpectInsert(Article::class, $defaults);
        $article = new Article();

        $article->id = 1337;
        $article->created = date('c', strtotime('-1 Hour'));
        $article->save();

        self::assertNotEquals($defaults, $article->getData());
    }

    /** @test */
    public function emulatesAutoIncrementWithRandomValue()
    {
        $this->ormExpectInsert(Article::class);
        $article = new Article();

        $article->save();

        self::assertGreaterThan(0, $article->id);
    }
}
