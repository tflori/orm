<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;
use ORM\Relation;

class RelationExample extends Entity
{
    protected static $relations = [
        'studlyCaps' => [
            Relation::OPT_CARDINALITY => 'many',
            Relation::OPT_CLASS => StudlyCaps::class,
            Relation::OPT_REFERENCE => ['studlyCapsId' => 'id'],
        ],
        'psr0StudlyCaps' => [
            Relation::OPT_CLASS => Psr0_StudlyCaps::class,
            Relation::OPT_REFERENCE => ['psr0StudlyCaps' => 'id'],
        ],
        'contactPhones' => [
            Relation::OPT_CLASS => ContactPhone::class,
            Relation::OPT_OPPONENT => 'relation',
        ],
        'dmgd' => [DamagedABBRVCase::class, ['dmgdId' => 'id']],
        'invalid' => ['many', StudlyCaps::class, 'opponent'], // many has to be omitted
        'mySnake' => ['one', Snake_Ucfirst::class, 'relation'],
        'snake' => [Snake_Ucfirst::class, ['snakeId' => 'id']]
    ];
}
