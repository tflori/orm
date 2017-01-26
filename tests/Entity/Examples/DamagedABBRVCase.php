<?php

namespace ORM\Test\Entity\Examples;

class DamagedABBRVCase extends TestEntity
{
    protected static $relations = [
        'relation' => ['one', RelationExample::class, 'dmgd'],
        'undefined1t1' => ['one', StudlyCaps::class, 'dmgd'],
        'undefined1tm' => [StudlyCaps::class, 'dmgd']
    ];
}
