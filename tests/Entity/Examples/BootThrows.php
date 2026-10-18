<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class BootThrows extends Entity
{
    protected static function boot()
    {
        parent::boot();
        static::$relations['leaked'] = [User::class, ['userId' => 'id']];
        throw new \RuntimeException('boot failed');
    }
}
