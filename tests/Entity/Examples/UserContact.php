<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class UserContact extends Entity
{
    protected static $relations = [
        'user' => [User::class, ['userId' => 'id']],
    ];
}
