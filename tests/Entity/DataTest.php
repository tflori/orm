<?php

namespace ORM\Test\Entity;

use Mockery\Mock;
use ORM\Entity;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StudlyCaps;

class DataTest extends TestCase
{
    public function testOnChangeDisabledByDefault()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldNotReceive('onChange');

        $mock->someVar = 'foobar';
    }

    public function testOnChangeGetCalled()
    {
        /** @var Mock|Entity $mock */
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->enableLifeCycle();
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
        $mock->enableLifeCycle();

        $mock->shouldNotReceive('onChange');

        $mock->someVar = 'foobar';
    }

    public function testStoresDataInCorrentNamingScheme()
    {
        Entity::$namingSchemeTable = 'snake_lower';
        $studlyCaps                = new StudlyCaps();

        $studlyCaps->someVar = 'foobar';

        self::assertSame([
            'some_var' => 'foobar'
        ], $studlyCaps->getRawData());
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
        $mock->enableLifeCycle();

        $mock->anotherVar = 'foobar';
    }

    public function testReturnsAnotherVar()
    {
        $mock = \Mockery::mock(StudlyCaps::class)->makePartial();
        $mock->shouldReceive('getAnotherVar')->once()->andReturn('foobar');

        self::assertSame('foobar', $mock->anotherVar);
    }

    public function testUsesNamingSchemeMethods()
    {
        Entity::$namingSchemeMethods = 'snake_lower';
        $mock = \Mockery::mock(Snake_Ucfirst::class)->makePartial();
        $mock->shouldReceive('set_another_var')->once()->with('foobar');
        $mock->shouldReceive('get_another_var')->atLeast()->once();

        $mock->another_var = 'foobar';
    }
}
