<?php

namespace ORM\Test\Entity;

use Mockery\Mock;
use ORM\Dbal\Column;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error\NotNullable;
use ORM\Dbal\Error\NotValid;
use ORM\Dbal\Table;
use ORM\Dbal\Type\Number;
use ORM\Entity;
use ORM\Exception;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UnknownColumn;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestEntity;
use ORM\Test\TestCase;

class DataTest extends TestCase
{
    public function tearDown()
    {
        StudlyCaps::disableValidator();
        parent::tearDown();
    }


    public function testOnChangeGetCalled()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onChange')->once()->with('someVar', null, 'foobar');

        $mock->someVar = 'foobar';
    }

    public function testStoresData()
    {
        $studlyCaps = new StudlyCaps();

        $studlyCaps->someVar = 'foobar';

        self::assertSame('foobar', $studlyCaps->someVar);
    }

    public function testShouldNotCallIfNotChanged()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->someVar = 'foobar';

        $mock->shouldNotReceive('onChange');

        $mock->someVar = 'foobar';
    }

    public function testStoresDataInCorrectNamingScheme()
    {
        $entity = new StudlyCaps();

        $entity->someVar = 'foobar';

        self::assertSame(['some_var' => 'foobar'], $entity->getData());
    }

    public function testDelegatesToSetter()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('setAnotherVar')->once()->with('foobar');

        $mock->anotherVar = 'foobar';
    }

    public function testOnChangeWatchesDataChanges()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldNotReceive('onChange');

        $mock->anotherVar = 'foobar';
    }

    public function testReturnsAnotherVar()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('getAnotherVar')->once()->andReturn('foobar');

        self::assertSame('foobar', $mock->anotherVar);
    }

    public function testCallsGetRelatedWhenThereIsARelationButNoValue()
    {
        /** @var Entity|Mock $entity */
        $entity = \Mockery::mock(RelationExample::class)->makePartial();
        $entity->setEntityManager($this->em);
        $related = [new StudlyCaps(), new StudlyCaps()];
        $entity->shouldReceive('getRelated')->with('studlyCaps')->once()->andReturn($related);

        $result = $entity->studlyCaps;

        self::assertSame($related, $result);
    }

    public function testUsesNamingSchemeMethods()
    {
        Entity::setNamingSchemeMethods('snake_lower');
        $mock = \Mockery::mock(Snake_Ucfirst::class)->makePartial();
        $mock->shouldReceive('set_another_var')->once()->with('foobar');
        $mock->shouldReceive('get_another_var')->atLeast()->once();

        $mock->another_var = 'foobar';
    }

    public function testGetsInitialDataOverConstructor()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);

        self::assertSame(42, $studlyCaps->id);
        self::assertSame('foobar', $studlyCaps->someVar);
    }

    public function testDoesNotOverwriteDefaultData()
    {
        $staticTableName = new StaticTableName();

        self::assertSame('default', $staticTableName->foo);
    }

    public function testItIsNotDirtyAfterCreateFromDatabase()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);

        self::assertFalse($studlyCaps->isDirty());
    }

    public function testIsDirtyAfterChange()
    {
        $studlyCaps = new StudlyCaps();

        $studlyCaps->someVar = 'foobar';

        self::assertTrue($studlyCaps->isDirty());
    }

    public function testOnlyTheChangedColumnsAreDirty()
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

    public function testResetRestoresOriginalData()
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

    public function testResetRestoresSpecificData()
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

    public function testResetDeletesData()
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

    public function testIsDirtyWithNewOriginalData()
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

    public function testIsNotDirtyWithDifferentOrder()
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

    public function testOnInitGetCalled()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onInit')->once()->with(true);

        $mock->__construct();
    }

    public function testOnInitFromDatabase()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onInit')->once()->with(false);

        $mock->__construct([
            'id' => 42,
            'some_var' => 'foobar'
        ], $this->em, true);
    }

    private $serialized = 'C:35:"ORM\Test\Entity\Examples\StudlyCaps":' .
                          '46:{a:2:{i:0;a:1:{s:3:"foo";s:3:"bar";}i:1;a:0:{}}}';

    public function testSerialization()
    {
        $entity = new StudlyCaps(['foo' => 'bar'], $this->em);

        $serialized = serialize($entity);

        self::assertSame($this->serialized, $serialized);
    }

    public function testDeserialization()
    {
        $entity = unserialize($this->serialized);

        self::assertInstanceOf(StudlyCaps::class, $entity);
        self::assertSame('bar', $entity->foo);
    }

    public function testUnserializeCallsOnInit()
    {
        $entity = \Mockery::mock(StudlyCaps::class)->makePartial();

        $entity->shouldReceive('onInit')->with(false)->once();

        $entity->unserialize(serialize([['foo' => 'bar'],[]]));
    }

    public function testDoesNotValidateValues()
    {
        $this->mocks['em']->shouldNotReceive('describe');

        $entity = new StudlyCaps();
        $entity->title = 42;
    }

    public function testValidatesValues()
    {
        StudlyCaps::enableValidator();

        $table = \Mockery::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->once()->andReturn($table);
        $table->shouldReceive('validate')->with('title', 'Hello World!')->once()->andReturn(true);

        $entity = new StudlyCaps();
        $entity->title = 'Hello World!';
    }

    public function testSetThrowsForUnknownColumns()
    {
        StudlyCaps::enableValidator();
        $table = \Mockery::mock(Table::class, [[]])->makePartial();
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);

        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown column title');

        $entity = new StudlyCaps();
        $entity->title = 'Hello World!';
    }

    public function testSetThrowsForInvalidValues()
    {
        StudlyCaps::enableValidator();
        $table = \Mockery::mock(Table::class, [[]])->makePartial();
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

    public function testFillPassesToSetAndValidates()
    {
        StudlyCaps::enableValidator();

        $table = \Mockery::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->with('field_a', 'valueA')->once()->andReturn(true);
        $table->shouldReceive('validate')->with('field_b', 'valueB')->once()->andReturn(true);

        $entity = new StudlyCaps();
        $entity->fill([
            'fieldA' => 'valueA',
            'fieldB' => 'valueB'
        ]);
    }

    public function testFillCanIgnoreUnknownColumns()
    {
        StudlyCaps::enableValidator();

        $table = \Mockery::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->andReturn($table);
        $table->shouldReceive('validate')->twice()->andThrow(UnknownColumn::class, 'unknown column');

        $entity = new StudlyCaps();
        $entity->fill([
            'fieldA' => 'valueA',
            'fieldB' => 'valueB'
        ], true);
    }

    public function testFillThrowsForUnknownColumns()
    {
        StudlyCaps::enableValidator();

        $table = \Mockery::mock(Table::class);
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

    public function testFillThrowsForMissingColumns()
    {
        StudlyCaps::enableValidator();

        $table = \Mockery::mock(Table::class, [[
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
