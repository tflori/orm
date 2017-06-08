<?php

namespace ORM\Test\Relation;

use ORM\EntityFetcher;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\UndefinedRelation;
use ORM\Relation\OneToOne;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\TestCase;

class OneToOneTest extends TestCase
{
    public function testGetsReturnedByGetRelation()
    {
        $result = DamagedABBRVCase::getRelation('relation');

        self::assertInstanceOf(OneToOne::class, $result);
    }

    public function testFetchFiltersByForeignKeyAndReturnsFirst()
    {
        $entity = new DamagedABBRVCase(['id' => 42], $this->em);
        $related = new RelationExample();
        $fetcher = \Mockery::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(RelationExample::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('dmgdId', 42)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('one')->with()->once()->andReturn($related);

        $result = $entity->fetch('relation');

        self::assertSame($related, $result);
    }

    public function testFetchThrowsWhenOpponentIsNotDefined()
    {
        $entity = new DamagedABBRVCase([], $this->em);

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation dmgd is not defined');

        $entity->fetch('undefined1t1');
    }

    public function testFetchThrowsWhenReferenceInOpponentIsNotDefined()
    {
        $entity = new Snake_Ucfirst([], $this->em);

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Reference is not defined in opponent');

        $entity->fetch('relation');
    }
}
