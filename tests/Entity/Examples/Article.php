<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Article extends Entity
{
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category']
    ];
}
