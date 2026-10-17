<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;
use ORM\Relation\Owner;

class BootMembership extends Entity
{
    protected static function boot()
    {
        parent::boot();
        static::defineRelation('role', new Owner(Category::class, ['categoryId' => 'id']));
    }
}
