<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Relations extends Entity
{
    protected static $relations = [
        'studlycaps' => [
            self::OPT_RELATION_CARDINALITY => 'one',
            self::OPT_RELATION_CLASS => StudlyCaps::class, 
            self::OPT_RELATION_RELATION => ['studlyCapsId' => 'id']
        ],
    ];
}
