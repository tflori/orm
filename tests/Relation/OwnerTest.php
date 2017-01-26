<?php

namespace ORM\Test\Relation;

use ORM\Relation\Owner;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\Relation;
use ORM\Test\TestCase;

class OwnerTest extends TestCase
{

    public function testGetsReturnedByGetRelation()
    {
        $result = Relation::getRelation('dmgd');

        self::assertInstanceOf(Owner::class, $result);
    }

    public function testFetchFetchesWithPrimaryKeyFor1T1Owner()
    {
        $entity = new Relation(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd');

        self::assertSame($related, $result);
    }

    public function testFetchReturnsNullWhenReferenceIsEmpty()
    {
        $entity = new Relation([], $this->em);

        $result = $entity->fetch('dmgd');

        self::assertNull($result);
    }

    public function testFetchAllReturnsTheEntity()
    {
        $entity = new Relation(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd', null, true);

        self::assertSame($related, $result);
    }
}
