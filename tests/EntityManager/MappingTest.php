<?php

namespace ORM\Test\EntityManager;

use ORM\EntityFetcher;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\NoEntity;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class MappingTest extends TestCase
{
    public function testStoresEntities()
    {
        $entity = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);

        $this->em->map($entity);

        self::assertSame($entity, $this->em->fetch(StudlyCaps::class, 42));
    }

    public function testStoresSeparatedById()
    {
        $e1 = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);
        $e2 = new StudlyCaps([
            'id' => 1,
            'some_var' => 'foobaz'
        ]);

        $this->em->map($e2);
        $this->em->map($e1);

        self::assertSame($e2, $this->em->fetch(StudlyCaps::class, 1));
        self::assertSame($e1, $this->em->fetch(StudlyCaps::class, 42));
    }

    public function testStoresSeparatedByClass()
    {
        $e1 = new StudlyCaps([
            'id' => 42,
            'some_var' => 'foobar'
        ]);
        $e2 = new Psr0_StudlyCaps([
            'id' => 42,
            'another_var' => 'foobar'
        ]);

        $this->em->map($e1);
        $this->em->map($e2);

        self::assertSame($e1, $this->em->fetch(StudlyCaps::class, 42));
        self::assertSame($e2, $this->em->fetch(Psr0_StudlyCaps::class, 42));
    }

    public function testStoresByDifferentPrimaryKey()
    {
        $e1 = new ContactPhone([
            'id' => 1,
            'name' => 'mobile'
        ]);
        $e2 = new ContactPhone([
            'id' => 1,
            'name' => 'private'
        ]);

        $this->em->map($e1);
        $this->em->map($e2);

        self::assertSame($e1, $this->em->fetch(ContactPhone::class, [1, 'mobile']));
        self::assertSame($e2, $this->em->fetch(ContactPhone::class, [1, 'private']));
    }

    public function testMapReturnsPreviouslyStored()
    {
        $e1 = new ContactPhone([
            'id' => 1,
            'name' => 'mobile'
        ]);
        $e2 = new ContactPhone([
            'id' => 1,
            'name' => 'mobile'
        ]);
        $this->em->map($e1);

        $entity = $this->em->map($e2);

        self::assertSame($e1, $entity);
        self::assertNotSame($e2, $entity);
        self::assertSame($e1, $this->em->fetch(ContactPhone::class, [1, 'mobile']));
    }

    public function testFetchReturnsEntityFetcher()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    public function testMapThrowsIfPrimaryKeyIsIncomplete()
    {
        $e1 = new ContactPhone([
            'id' => 42
        ]);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Incomplete primary key - missing name');

        $this->em->map($e1);
    }

    public function testThrowsForNonEntity()
    {
        self::expectException(NoEntity::class);
        self::expectExceptionMessage(self::class . ' is not a subclass of Entity');

        $this->em->fetch(self::class, 1);
    }

    public function testFetchThrowsIfPrimaryKeyIsIncomplete()
    {
        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Primary key consist of [id,name] only 1 given');

        $this->em->fetch(ContactPhone::class, 42);
    }

    public function testFetchGetsEntityFromPrimaryKey()
    {
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT DISTINCT t0.* FROM "studly_caps" AS t0 WHERE "t0"."id" = 42')
            ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->with(\PDO::FETCH_ASSOC)->andReturn(
            ['id' => 42, 'col1' => 'hallo', 'col2' => 'welt']
        );

        $studlyCaps = $this->em->fetch(StudlyCaps::class, 42);

        self::assertInstanceOf(StudlyCaps::class, $studlyCaps);
    }
}
