<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class RelationExample extends Entity
{
    protected static $relations = [
        'studlyCaps' => [
            self::OPT_RELATION_CARDINALITY => 'many',
            self::OPT_RELATION_CLASS => StudlyCaps::class,
            self::OPT_RELATION_REFERENCE => ['studlyCapsId' => 'id'],
        ],
        'psr0StudlyCaps' => [
            self::OPT_RELATION_CLASS => Psr0_StudlyCaps::class,
            self::OPT_RELATION_REFERENCE => ['psr0StudlyCaps' => 'id'],
        ],
        'contactPhones' => [
            self::OPT_RELATION_CLASS => ContactPhone::class,
            self::OPT_RELATION_OPPONENT => 'relation',
        ],
        'dmgd' => [DamagedABBRVCase::class, ['dmgdId' => 'id']],
        'invalid' => ['many', StudlyCaps::class, 'opponent'], // many has to be omitted
        'mySnake' => ['one', Snake_Ucfirst::class, 'relation'],
        'snake' => [Snake_Ucfirst::class, ['snakeId' => 'id']]
    ];
}
