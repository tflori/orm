<?php

namespace ORM\Test\Entity;

use Mockery as m;
use Mockery\Mock;
use ORM\Dbal\Column;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error\NotNullable;
use ORM\Dbal\Error\NotValid;
use ORM\Dbal\Table;
use ORM\Dbal\Type\Number;
use ORM\Entity;
use ORM\Exception;
use ORM\Exception\UnknownColumn;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;
use ORM\Testing\MocksEntityManager;

class DataTest extends TestCase
{
    use MocksEntityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mocks['em'] = $this->ormInitMock();
    }

    public function tearDown(): void
    {
        StudlyCaps::disableValidator();
        parent::tearDown();
    }


    /** @test */
    public function onChangeGetCalled()
    {
        /** @var Mock|Entity $mock */
        $mock = $this->ormCreateMockedEntity(StudlyCaps::class);
        $mock->shouldReceive('onChange')->once()->with('someVar', null, 'foobar');

        $mock->someVar = 'foobar';
    }

    /** @test */
    public function storesData()
    {
        $studlyCaps = new StudlyCaps();

        $studlyCaps->someVar = 'foobar';

        self::assertSame('foobar', $studlyCaps->someVar);
    }

    /** @test */
    public function shouldNotCallIfNotChanged()
    {
        /** @var Mock|Entity $mock */
        $mock = $this->ormCreateMockedEntity(StudlyCaps::class);
        $mock->someVar = 'foobar';

        $mock->shouldNotReceive('onChange');

        $mock->someVar = 'foobar';
    }

    /** @test */
    public function storesDataInCorrectNamingScheme()
    {
        $entity = new StudlyCaps();

        $entity->someVar = 'foobar';

        self::assertSame(['some_var' => 'foobar'], $entity->getData());
    }

    /** @test */
    public function delegatesToSetter()
    {
        $mock = $this->ormCreateMockedEntity(StudlyCaps::class);
        $mock->shouldReceive('setAnotherVar')->once()->with('foobar');

        $mock->anotherVar = 'foobar';
    }

    /** @test */
    public function onChangeWatchesOnlyDataChanges()
    {
        /** @var Mock|Entity $mock */
        $mock = $this->ormCreateMockedEntity(StudlyCaps::class);
        $mock->shouldNotReceive('onChange');

        // anotherVar is a property not an attribute
        $mock->anotherVar = 'foobar';
    }

    /** @test */
    public function returnsAnotherVar()
    {
        $mock = $this->ormCreateMockedEntity(StudlyCaps::class);
        $mock->shouldReceive('getAnotherVar')->once()->andReturn('foobar');

        self::assertSame('foobar', $mock->anotherVar);
    }

    /** @test */
    public function callsGetRelatedWhenThereIsARelationButNoValue()
    {
        /** @var Entity|Mock $entity */
        $entity = m::mock(RelationExample::class)->makePartial();
        $entity->setEntityManager($this->em);
        $related = [new StudlyCaps(), new StudlyCaps()];
        $entity->shouldReceive('getRelated')->with('studlyCaps')->once()->andReturn($related);

        $result = $entity->studlyCaps;

        self::assertSame($related, $result);
    }

    /** @test */
    public function usesNamingSchemeMethods()
    {
        Entity::setNamingSchemeMethods('snake_lower');
        $mock = $this->ormCreateMockedEntity(Snake_Ucfirst::class);
        $mock->shouldReceive('set_another_var')->once()->with('foobar');
        $mock->shouldReceive('get_another_var')->atLeast()->once();

        $mock->another_var = 'foobar';
    }

    /** @test */
    public function getsInitialDataOverConstructor()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);

        self::assertSame(42, $studlyCaps->id);
        self::assertSame('foobar', $studlyCaps->someVar);
    }

    /** @test */
    public function doesNotOverwriteDefaultData()
    {
        $staticTableName = new StaticTableName();

        self::assertSame('default', $staticTableName->foo);
    }

    /** @test */
    public function itIsNotDirtyAfterCreateFromDatabase()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);

        self::assertFalse($studlyCaps->isDirty());
    }

    /** @test */
    public function isDirtyAfterChange()
    {
        $studlyCaps = new StudlyCaps();

        $studlyCaps->someVar = 'foobar';

        self::assertTrue($studlyCaps->isDirty());
    }

    /** @test */
    public function onlyTheChangedColumnsAreDirty()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);

        $studlyCaps->someVar = 'foobaz';
        $studlyCaps->newVar = 'foobar';

        self::assertTrue($studlyCaps->isDirty('someVar'));
        self::assertTrue($studlyCaps->isDirty('newVar'));
        self::assertFalse($studlyCaps->isDirty('id'));
        self::assertFalse($studlyCaps->isDirty('nonExistingVar'));
    }

    /** @test */
    public function everyAttributeIsDirtyInNewEntities()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar', 'created' => '2018-05-23T18:52:42Z']);

        $dirty = $article->getDirty();

        self::assertArrayHasKey('id', $dirty);
        self::assertArrayHasKey('title', $dirty);
        self::assertArrayHasKey('created', $dirty);
    }

    /** @test */
    public function getDirtyReturnsAttributeNames()
    {
        $article = new Article(['id' => 23, 'article_title' => 'Foo Bar'], $this->em, true);
        $article->articleTitle = 'new title';

        $dirty = $article->getDirty();

        // article uses camelCase attribute naming...
        self::assertArrayHasKey('articleTitle', $dirty);
    }

    /** @test */
    public function getDirtyReturnsAnArrayWithOldAndNewValues()
    {
        $article = new Article(['id' => 23, 'title' => 'old title'], $this->em, true);
        $article->title = 'new title';

        $dirty = $article->getDirty();

        self::assertSame(['old title', 'new title'], $dirty['title']);
    }

    /** @test */
    public function getDirtyDoesNotShowExcludedAttributes()
    {
        $article = new Article(['id' => 23, 'userId' => 42, 'title' => 'old title'], $this->em, true);
        $article->userId = 1;

        $dirty = $article->getDirty();

        self::assertArrayNotHasKey('userId', $dirty);
    }

    /** @test */
    public function resetRestoresOriginalData()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);
        $studlyCaps->someVar = 'foobaz';
        $studlyCaps->newVar = 'foobar';

        $studlyCaps->reset();

        self::assertSame('foobar', $studlyCaps->someVar);
        self::assertNull($studlyCaps->newVar);
    }

    /** @test */
    public function resetRestoresSpecificData()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);
        $studlyCaps->someVar = 'foobaz';
        $studlyCaps->newVar = 'foobar';

        $studlyCaps->reset('someVar');

        self::assertSame('foobar', $studlyCaps->someVar);
        self::assertSame('foobar', $studlyCaps->newVar);
    }

    /** @test */
    public function resetDeletesData()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);
        $studlyCaps->someVar = 'foobaz';
        $studlyCaps->newVar = 'foobar';

        $studlyCaps->reset('newVar');

        self::assertSame('foobaz', $studlyCaps->someVar);
        self::assertNull($studlyCaps->newVar);
    }

    /** @test */
    public function isDirtyWithNewOriginalData()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);

        $studlyCaps->setOriginalData([
            'id' => 42,
            'some_var' => 'foobaz'
        ]);

        self::assertTrue($studlyCaps->isDirty());
        self::assertFalse($studlyCaps->isDirty('id'));
        self::assertTrue($studlyCaps->isDirty('someVar'));
    }

    /** @test */
    public function isNotDirtyWithDifferentOrder()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);

        $studlyCaps->setOriginalData([
            'some_var' => 'foobar',
            'id' => 42
        ]);

        self::assertFalse($studlyCaps->isDirty());
    }

    /** @test */
    public function onInitGetCalled()
    {
        $mock = m::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onInit')->once()->with(true);

        $mock->__construct();
    }

    /** @test */
    public function onInitFromDatabase()
    {
        $mock = m::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onInit')->once()->with(false);

        $mock->__construct([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);
    }

    /** @test */
    public function serialization()
    {
        $entity = new StudlyCaps(['foo' => 'bar'], $this->em, true);

        $serialized = $entity->serialize();

        self::assertSame(serialize([['foo' => 'bar'], [], true]), $serialized);
    }

    /** @test */
    public function deserialization()
    {
        $serialized = serialize(new StudlyCaps(['foo' => 'bar']));

        $entity = unserialize($serialized);

        self::assertInstanceOf(StudlyCaps::class, $entity);
        self::assertSame('bar', $entity->foo);
    }

    /** @test */
    public function unserializeCallsOnInit()
    {
        /** @var Entity|m\MockInterface $entity */
        $entity = m::mock(StudlyCaps::class)->makePartial();

        $entity->shouldReceive('onInit')->with(false)->once();

        $entity->unserialize(serialize([['foo' => 'bar'], [], true]));
    }

    /** @test */
    public function doesNotValidateValues()
    {
        $this->mocks['em']->shouldNotReceive('describe');

        $entity = new StudlyCaps();
        $entity->title = 42;
    }

    /** @test */
    public function validatesValues()
    {
        StudlyCaps::enableValidator();

        $table = m::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->once()->andReturn($table);
        $table->shouldReceive('validate')->with('title', 'Hello World!')->once()->andReturn(true);

        $entity = new StudlyCaps();
        $entity->title = 'Hello World!';
    }

    /** @test */
    public function setThrowsForUnknownColumns()
    {
        StudlyCaps::enableValidator();
        $table = m::mock(Table::class, [[]])->makePartial();
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);

        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown column title');

        $entity = new StudlyCaps();
        $entity->title = 'Hello World!';
    }

    /** @test */
    public function setThrowsForInvalidValues()
    {
        StudlyCaps::enableValidator();
        $table = m::mock(Table::class, [[]])->makePartial();
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->with('title', 42)->andReturn(new NotValid(
            new Column($this->dbal, ['column_name' => 'title']),
            new NoString(['type' => 'varchar'])
        ));

        self::expectException(NotValid::class);
        self::expectExceptionMessage('Value not valid for title');

        $entity = new StudlyCaps();
        $entity->title = 42;
    }

    /** @test */
    public function fillPassesToSetAndValidates()
    {
        StudlyCaps::enableValidator();

        $table = m::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->with('field_a', 'valueA')->once()->andReturn(true);
        $table->shouldReceive('validate')->with('field_b', 'valueB')->once()->andReturn(true);

        $entity = new StudlyCaps();
        $entity->fill([
            'fieldA' => 'valueA',
            'fieldB' => 'valueB'
        ]);
    }

    /** @test */
    public function fillCanIgnoreUnknownColumns()
    {
        StudlyCaps::enableValidator();

        $table = m::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->twice()->andThrow(UnknownColumn::class, 'unknown column');

        $entity = new StudlyCaps();
        $entity->fill([
            'fieldA' => 'valueA',
            'fieldB' => 'valueB'
        ], true);
    }

    /** @test */
    public function fillThrowsForUnknownColumns()
    {
        StudlyCaps::enableValidator();

        $table = m::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->once()->andThrow(UnknownColumn::class, 'Unknown column field_a');

        self::expectException(UnknownColumn::class);
        self::expectExceptionMessage('Unknown column field_a');

        $entity = new StudlyCaps();
        $entity->fill([
            'fieldA' => 'valueA',
            'fieldB' => 'valueB'
        ]);
    }

    /** @test */
    public function fillThrowsForMissingColumns()
    {
        StudlyCaps::enableValidator();

        $table = m::mock(Table::class, [[
            // id is auto increment
            new Column($this->dbal, [
                'column_name' => 'id',
                'column_default' => 'sequence(AUTO_INCREMENT)',
                'is_nullable' => false
            ]),
            // title is missing
            new Column($this->dbal, [
                'column_name' => 'title',
                'column_default' => null,
                'is_nullable' => false
            ]),
            // intro is nullable
            new Column($this->dbal, [
                'column_name' => 'intro',
                'column_default' => null,
                'is_nullable' => true
            ]),
            // user (writer) is given
            new Column($this->dbal, [
                'column_name' => 'user_id',
                'column_default' => null,
                'is_nullable' => false,
                'type' => Number::class,
            ]),
        ]])->makePartial();
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);

        self::expectException(NotNullable::class);
        self::expectExceptionMessage('title does not allow null values');

        $entity = new StudlyCaps();
        $entity->fill([
            'userId' => 23
        ], false, true);
    }
}
