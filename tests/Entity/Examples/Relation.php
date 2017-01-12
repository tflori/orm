<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Relation extends Entity
{
    protected static $relations = [
        'studlyCaps' => [
            self::OPT_RELATION_CARDINALITY => 'one',
            self::OPT_RELATION_CLASS => StudlyCaps::class, 
            self::OPT_RELATION_REFERENCE => ['studlyCapsId' => 'id'],
        ],
        'psr0StudlyCaps' => [
            self::OPT_RELATION_CLASS => Psr0_StudlyCaps::class,
            self::OPT_RELATION_REFERENCE => ['psr0StudlyCaps' => 'id'],
        ],
        'testEntities' => [
            self::OPT_RELATION_CLASS => TestEntity::class,
            self::OPT_RELATION_OPPONENT => 'relation',
        ],
    ];
}
