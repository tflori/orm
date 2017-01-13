<?php

namespace ORM\Test\Entity\Examples;

class DamagedABBRVCase extends TestEntity
{
    protected static $relations = [
        'relation' => ['one', Relation::class, 'dmgd']
    ];
}
