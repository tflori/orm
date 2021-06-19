<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class User extends Entity
{
    protected static $relations = [
        'articles' => [Article::class, 'writer'],
        'contact' => ['one', UserContact::class, 'user'],
    ];
}
