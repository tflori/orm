<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class BootShare extends Entity
{
    protected static function boot()
    {
        parent::boot();
        static::$relations['role'] = [User::class, ['userId' => 'id']];
    }
}
