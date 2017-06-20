<?php

namespace ORM\Test\Entity;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\NoEntityManager;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class SaveEntityTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->em->shouldReceive('sync')->andReturn(false)->byDefault();
    }

    public function testUsesEntityManagerFromConstructor()
    {
        $entity = new StudlyCaps(['foo' => 'bar'], $this->em);

        $this->em->shouldReceive('sync')->with($entity)->once()->andThrow(new IncompletePrimaryKey('Foobar'));
        $this->em->shouldReceive('insert')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $var = $entity::getPrimaryKeyVars()[0];
            $column = $entity::getColumnName($var);

            $entity->setOriginalData(array_merge($entity->getData(), [
                $column => 42
            ]));
            $entity->__set($var, 42);
            return true;
        });

        $entity->save();

        self::assertSame(42, $entity->id);
    }

    public function testUsesEntityManagerFromSave()
    {
        $emMock = \Mockery::mock(EntityManager::class);
        $entity = new StudlyCaps(['foo' => 'bar'], $this->em);

        $emMock->shouldReceive('sync')->with($entity)->once()->andThrow(new IncompletePrimaryKey('Foobar'));
        $emMock->shouldReceive('insert')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $var = $entity::getPrimaryKeyVars()[0];
            $column = $entity::getColumnName($var);

            $entity->setOriginalData(array_merge($entity->getData(), [
                $column => 42
            ]));
            $entity->__set($var, 42);
            return true;
        });

        $entity->setEntityManager($emMock);
        $entity->save();

        self::assertSame(42, $entity->id);
    }

    public function testThrowsWithoutPrimaryAndAutoincrement()
    {
        $entity = new Psr0_StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('sync')->with($entity)->andThrow(IncompletePrimaryKey::class, 'Foobar');

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Foobar');

        $entity->save();
    }

    public function testSyncsTheEntityAndStopsWhenNotDirty()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'bar']);
            return true;
        });
        $this->em->shouldNotReceive('update');

        $entity->save();
    }

    public function testUpdatesIfDirty()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'baz']);
            return true;
        });
        $this->em->shouldReceive('update')->with($entity)->once();
        $this->em->shouldReceive('sync')->with($entity, true);

        $entity->save();
    }

    public function testInsertsIfNotPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturn(false);
        $this->em->shouldReceive('insert')->with($entity, false)->once();
        $this->em->shouldReceive('sync')->with($entity, true);

        $entity->save();
    }

    public function testCallsPrePersistBeforeInsert()
    {
        $entity = \Mockery::mock(StudlyCaps::class . '[prePersist]', [['foo' => 'bar'], $this->em])->makePartial();
        $entity->shouldReceive('prePersist')->once();

        $this->em->shouldReceive('sync')->with($entity)->once()->andThrow(new IncompletePrimaryKey('Foobar'));
        $this->em->shouldReceive('insert')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $var = $entity::getPrimaryKeyVars()[0];
            $column = $entity::getColumnName($var);

            $entity->setOriginalData(array_merge($entity->getData(), [
                $column => 42
            ]));
            $entity->__set($var, 42);
            return true;
        });

        $entity->save();
    }

    public function testCallsPreUpdateBeforeUpdate()
    {
        $entity = \Mockery::mock(StudlyCaps::class . '[preUpdate]', [['id' => 42, 'foo' => 'bar'], $this->em]);
        $entity->shouldReceive('preUpdate')->once();

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'baz']);
            return true;
        });
        $this->em->shouldReceive('update')->with($entity)->once();
        $this->em->shouldReceive('sync')->with($entity, true);

        $entity->save();
    }

    public function testCallsPostPersistAfterInsert()
    {
        $entity = \Mockery::mock(StudlyCaps::class . '[postPersist]', [['foo' => 'bar'], $this->em])->makePartial();
        $entity->shouldReceive('postPersist')->once();

        $this->em->shouldReceive('sync')->with($entity)->once()->andThrow(new IncompletePrimaryKey('Foobar'));
        $this->em->shouldReceive('insert')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $var = $entity::getPrimaryKeyVars()[0];
            $column = $entity::getColumnName($var);

            $entity->setOriginalData(array_merge($entity->getData(), [
                $column => 42
            ]));
            $entity->__set($var, 42);
            return true;
        });

        $entity->save();
    }

    public function testCallsPostUpdateAfterUpdate()
    {
        $entity = \Mockery::mock(StudlyCaps::class . '[postUpdate]', [['id' => 42, 'foo' => 'bar'], $this->em]);
        $entity->shouldReceive('postUpdate')->once();

        $this->em->shouldReceive('sync')->with($entity)->once()->andReturnUsing(function (Entity $entity) {
            $entity->setOriginalData(['id' => 42, 'foo' => 'baz']);
            return true;
        });
        $this->em->shouldReceive('update')->with($entity)->once()->andReturn(true);

        $entity->save();
    }
}
