<?php

namespace ORM\Test\Entity\Examples;

use ORM\Test\TestEntity;

class ContactPhone extends TestEntity
{
    protected static $primaryKey = ['id', 'name'];

    protected static $relations = [
        'relation' => [RelationExample::class, ['relationId' => 'id']],
    ];
}
