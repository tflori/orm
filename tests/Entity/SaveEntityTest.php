<?php

namespace ORM\Test\Entity;

use ORM\Entity;
use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class SaveEntityTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    public function testSavesNewEntityWithAutoincrement()
    {
        /** @var StudlyCaps $entity */
        $entity = new StudlyCaps(['foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity)->once()->andThrow(new IncompletePrimaryKey('Foobar'));
        $this->em->shouldReceive('insert')->with($entity)->once()->andReturn(42);
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $entity->save($this->em);

        self::assertSame(42, $entity->id);
    }

    public function testThrowsWithoutPrimaryAndAutoincrement()
    {
        $entity = new Psr0_StudlyCaps(['foo' => 'bar']);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Incomplete primary key - missing id');

        $entity->save($this->em);
    }

    public function testThrowsWithoutPrimaryAndMultipleKeys()
    {
        $staticTableName = new StaticTableName(['stn_table' => 'foobar']);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Incomplete primary key - missing name');

        $staticTableName->save($this->em);
    }

    public function testSyncsTheEntityAndStopsWhenNotDirty()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'bar']);
            return true;
        });
        $this->em->shouldNotReceive('update');

        $entity->save($this->em);
    }

    public function testUpdatesIfDirty()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'baz']);
            return true;
        });
        $this->em->shouldReceive('update')->with($entity)->once();

        $entity->save($this->em);
    }

    public function testInsertsIfNotPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturn(false);
        $this->em->shouldReceive('insert')->with($entity, false)->once();

        $entity->save($this->em);
    }
}
