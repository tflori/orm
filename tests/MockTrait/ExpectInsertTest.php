<?php

namespace ORM\Test\MockTrait;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;

class ExpectInsertTest extends MockeryTestCase
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

    public function testAllowsInsertOfSpecifiedClass()
    {
        $this->ormExpectInsert(Article::class, ['id' => 42]);
        $article = new Article();

        $article->save();
    }

    public function testDoesNotAllowInsertsOfOtherClasses()
    {
        $this->ormExpectInsert(Category::class);
        $article = new Article();

        self::expectException(\BadMethodCallException::class);
        self::expectExceptionMessage('PDO::query() does not exist on this mock object');

        try {
            $article->save();
        } catch (\Exception $e) {
            \Mockery::resetContainer();
            throw $e;
        }
    }

    public function testSetsDefaultData()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->ormExpectInsert(Article::class, $defaults);
        $article = new Article();

        $article->save();

        self::assertSame($defaults, $article->getData());
    }

    public function testDoesNotOverwriteCurrentData()
    {
        $defaults = ['id' => 42, 'created' => date('c')];
        $this->ormExpectInsert(Article::class, $defaults);
        $article = new Article();

        $article->id = 1337;
        $article->created = date('c', strtotime('-1 Hour'));
        $article->save();

        self::assertNotEquals($defaults, $article->getData());
    }

    public function testEmulatesAutoIncrementWithRandomValue()
    {
        $this->ormExpectInsert(Article::class);
        $article = new Article();

        $article->save();

        self::assertGreaterThan(0, $article->id);
    }
}
