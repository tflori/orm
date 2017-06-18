<?php

namespace ORM\Test\MockTrait;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ORM\EntityManager;
use ORM\MockTrait;
use ORM\Test\Entity\Examples\Article;

class ExpectUpdateTest extends MockeryTestCase
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

    public function testExpectsSave()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectUpdate($article);

        self::expectException(m\Exception\InvalidCountException::class);

        m::close();
    }

    public function testDoesNotEmulateUpdateWhenNotDirty()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectUpdate($article);

        $article->shouldNotReceive('preUpdate');

        $this->updateEntity(Article::class, 42);
    }

    public function testUpdatesTheDataFromDatabase()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
        $this->ormExpectUpdate($article, [], ['title' => 'Don`t Panic!']);

        $article->shouldNotReceive('preUpdate');

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);
    }

    public function testEmulatesUpdate()
    {
        $article = $this->ormCreateMockedEntity(Article::class, [
            'id' => 42,
            'title' => 'Hello World!',
            'changed' => date('c', strtotime('-1 Hour')),
        ]);
        $changingData = ['changed' => date('c')];
        $this->ormExpectUpdate($article, $changingData);

        $article->shouldReceive('preUpdate')->once()->ordered()->passthru();
        $article->shouldReceive('setOriginalData')->with(m::subset(array_merge(
            $article->getData(),
            ['title' => 'Don`t Panic!'],
            $changingData
        )))->once()->ordered()->passthru();
        $article->shouldReceive('reset')->once()->ordered()->passthru();
        $article->shouldReceive('postUpdate')->once()->ordered()->passthru();

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);

//        self::assertSame($changingData['changed'], $article->changed);
    }

    /**
     * Update an entity with $data
     *
     * @param string $class
     * @param int    $id
     * @param array  $data
     */
    protected function updateEntity($class, $id, $data = [])
    {
        $entity = $this->em->fetch($class, $id);
        $entity->fill($data);
        $entity->save();
    }
}
