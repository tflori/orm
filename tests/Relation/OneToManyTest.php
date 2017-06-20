<?php

namespace ORM\Test\Relation;

use ORM\EntityFetcher;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UndefinedRelation;
use ORM\Relation\OneToMany;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\TestCase;

class OneToManyTest extends TestCase
{
    public function testGetsReturnedByGetRelation()
    {
        $result = RelationExample::getRelation('contactPhones');

        self::assertInstanceOf(OneToMany::class, $result);
    }

    public function testFetchFiltersByForeignKeyAndReturnsFetcher()
    {
        $entity = new RelationExample(['id' => 42], $this->em);
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->andReturn($fetcher);

        $result = $entity->fetch('contactPhones');

        self::assertSame($fetcher, $result);
    }

    public function testFetchThrowsWhenOpponentIsNotDefined()
    {
        $entity = new DamagedABBRVCase([], $this->em);

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation dmgd is not defined');

        $entity->fetch('undefined1tm');
    }

    public function testFetchThrowsWhenReferenceInOpponentIsNotDefined()
    {
        $entity = new Snake_Ucfirst([], $this->em);

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Reference is not defined in opponent');

        $entity->fetch('invalid');
    }

    public function testFetchReturnsAllWithGetAll()
    {
        $entity = new RelationExample(['id' => 42], $this->em);
        $related = [new ContactPhone(), new ContactPhone()];
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->passthru();
        $fetcher->shouldReceive('all')->with()->once()->andReturn($related);

        $result = $entity->fetch('contactPhones', true);

        self::assertSame($related, $result);
    }

    public function testFetchThrowsWhenKeyIsEmpty()
    {
        $entity = new RelationExample([], $this->em);

        self::expectException(\ORM\Exception\IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('contactPhones');
    }
}
