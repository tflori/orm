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
use ORM\Exception\UnknownColumn;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class DataTest extends TestCase
{
    public function tearDown()
    {
        StudlyCaps::disableValidator();
        parent::tearDown();
    }


    /** @test */
    public function onChangeGetCalled()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
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
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
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
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('setAnotherVar')->once()->with('foobar');

        $mock->anotherVar = 'foobar';
    }

    /** @test */
    public function onChangeWatchesDataChanges()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldNotReceive('onChange');

        $mock->anotherVar = 'foobar';
    }

    /** @test */
    public function returnsAnotherVar()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('getAnotherVar')->once()->andReturn('foobar');

        self::assertSame('foobar', $mock->anotherVar);
    }

    /** @test */
    public function callsGetRelatedWhenThereIsARelationButNoValue()
    {
        /** @var Entity|Mock $entity */
        $entity = \Mockery::mock(RelationExample::class)->makePartial();
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
        $mock = \Mockery::mock(Snake_Ucfirst::class)->makePartial();
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
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('onInit')->once()->with(true);

        $mock->__construct();
    }

    /** @test */
    public function onInitFromDatabase()
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

    /** @test */
    public function serialization()
    {
        $entity = new StudlyCaps(['foo' => 'bar'], $this->em);

        $serialized = serialize($entity);

        self::assertSame($this->serialized, $serialized);
    }

    /** @test */
    public function deserialization()
    {
        $entity = unserialize($this->serialized);

        self::assertInstanceOf(StudlyCaps::class, $entity);
        self::assertSame('bar', $entity->foo);
    }

    /** @test */
    public function unserializeCallsOnInit()
    {
        $entity = \Mockery::mock(StudlyCaps::class)->makePartial();

        $entity->shouldReceive('onInit')->with(false)->once();

        $entity->unserialize(serialize([['foo' => 'bar'],[]]));
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

        $table = \Mockery::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('studly_caps')->once()->andReturn($table);
        $table->shouldReceive('validate')->with('title', 'Hello World!')->once()->andReturn(true);

        $entity = new StudlyCaps();
        $entity->title = 'Hello World!';
    }

    /** @test */
    public function setThrowsForUnknownColumns()
    {
        StudlyCaps::enableValidator();
        $table = \Mockery::mock(Table::class, [[]])->makePartial();
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

    /** @test */
    public function fillPassesToSetAndValidates()
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

    /** @test */
    public function fillCanIgnoreUnknownColumns()
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

    /** @test */
    public function fillThrowsForUnknownColumns()
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

    /** @test */
    public function fillThrowsForMissingColumns()
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
