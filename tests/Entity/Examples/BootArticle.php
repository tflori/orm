<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class BootArticle extends Entity
{
    protected static function boot()
    {
        parent::boot();
        static::$relations['writer'] = [User::class, ['userId' => 'id']];
    }
}
