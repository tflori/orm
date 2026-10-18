<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Share extends Entity
{
    protected static function roleRelation()
    {
        return [User::class, ['userId' => 'id']];
    }
}
