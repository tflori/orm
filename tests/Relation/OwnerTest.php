<?php

namespace ORM\Test\Relation;

use ORM\Relation\Owner;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\TestCase;

class OwnerTest extends TestCase
{

    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = RelationExample::getRelation('dmgd');

        self::assertInstanceOf(Owner::class, $result);
    }

    /** @test */
    public function fetchFetchesWithPrimaryKeyFor1T1Owner()
    {
        $entity = new RelationExample(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd');

        self::assertSame($related, $result);
    }

    /** @test */
    public function fetchReturnsNullWhenReferenceIsEmpty()
    {
        $entity = new RelationExample([], $this->em);

        $result = $entity->fetch('dmgd');

        self::assertNull($result);
    }

    /** @test */
    public function fetchAllReturnsTheEntity()
    {
        $entity = new RelationExample(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd', true);

        self::assertSame($related, $result);
    }
}
