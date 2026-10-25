<?php

namespace ORM\Test\Testing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Testing\MocksEntityManager;
use ORM\Testing\SaveExpectation;

class ExpectUpdateTest extends MockeryTestCase
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
    public function expectsSave()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectUpdate($article);

        self::expectException(m\Exception\InvalidCountException::class);

        m::close();
    }

    /** @test */
    public function doesNotEmulateUpdateWhenNotDirty()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);
        $this->ormExpectUpdate($article);

        $article->shouldNotReceive('preUpdate');

        $this->updateEntity(Article::class, 42);
    }

    /** @test */
    public function updatesTheDataFromDatabase()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello World!']);
        $this->ormExpectUpdate($article, [], ['title' => 'Don`t Panic!']);

        $article->shouldNotReceive('preUpdate');

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);
    }

    /** @test */
    public function keepsTheDataFromDatabaseForUnchangedColumns()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article, [], ['text' => 'Bar']);

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);

        self::assertSame('Don`t Panic!', $article->title);
        self::assertSame('Bar', $article->text);
        self::assertFalse($article->isDirty());
    }

    /** @test */
    public function emulatesUpdate()
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

    /** @test */
    public function returnsASaveExpectation()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42]);

        $expectation = $this->ormExpectUpdate($article);

        self::assertInstanceOf(SaveExpectation::class, $expectation);
        $article->save();
    }

    /** @test */
    public function withChangesVerifiesTheChanges()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article)->withChanges(['title' => 'Don`t Panic!']);

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);
    }

    /** @test */
    public function withChangesFailsForOtherChanges()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article)->withChanges(['title' => 'Don`t Panic!']);

        self::expectException(m\Exception::class);
        self::expectExceptionMessage('save() of ' . Article::class . ' was expected to write');

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!', 'text' => 'Bar']);
    }

    /** @test */
    public function withChangesAcceptsMatchers()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article)->withChanges(m::subset(['title' => 'Don`t Panic!']));

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!', 'text' => 'Bar']);
    }

    /** @test */
    public function withChangesFailsWhenTheMatcherDoesNotMatch()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article)->withChanges(m::subset(['title' => 'Don`t Panic!']));

        self::expectException(m\Exception::class);
        self::expectExceptionMessage('<Subset');

        $this->updateEntity(Article::class, 42, ['text' => 'Bar']);
    }

    /** @test */
    public function withChangesAcceptsAttributeNames()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'user_id' => 1]);
        $this->ormExpectUpdate($article)->withChanges(['userId' => 2]);

        $this->updateEntity(Article::class, 42, ['userId' => 2]);
    }

    /** @test */
    public function withChangesMatchersGetAttributeNames()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'user_id' => 1]);
        $this->ormExpectUpdate($article)->withChanges(m::subset(['userId' => 2]));

        $this->updateEntity(Article::class, 42, ['userId' => 2]);
    }

    /** @test */
    public function withChangesAcceptsColumnNames()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'user_id' => 1]);
        $this->ormExpectUpdate($article)->withChanges(['user_id' => 2]);

        $this->updateEntity(Article::class, 42, ['userId' => 2]);
    }

    /** @test */
    public function withChangesVerifiesAfterPreUpdate()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $article->shouldReceive('preUpdate')->andReturnUsing(function () use ($article) {
            $article->setAttribute('text', 'Changed in preUpdate');
        });
        $this->ormExpectUpdate($article)->withChanges(['title' => 'Don`t Panic!', 'text' => 'Changed in preUpdate']);

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);
    }

    /** @test */
    public function withChangesIgnoresChangesInTheDatabase()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello', 'text' => 'Foo']);
        $this->ormExpectUpdate($article, [], ['text' => 'Bar'])->withChanges(['title' => 'Don`t Panic!']);

        $this->updateEntity(Article::class, 42, ['title' => 'Don`t Panic!']);
    }

    /** @test */
    public function withChangesFailsWhenNothingChanged()
    {
        $article = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'title' => 'Hello']);
        $this->ormExpectUpdate($article)->withChanges(['title' => 'Don`t Panic!']);

        self::expectException(m\Exception::class);

        $this->updateEntity(Article::class, 42);
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
