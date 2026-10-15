<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Membership extends Entity
{
    protected static function roleRelation()
    {
        return [Category::class, ['categoryId' => 'id']];
    }
}
