<?php

namespace ORM\Test\Entity\Examples;

class ContactPhone extends TestEntity
{
    protected static $primaryKey = ['id', 'name'];

    protected static $relations = [
        'relation' => [Relation::class, ['relationId' => 'id']],
    ];
}
