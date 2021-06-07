<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Image extends Entity implements Taggable
{
    protected static $relations = [
        'tags' => [Tag::class, 'parent'],
    ];
}
