<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Category extends Entity
{
    protected static $enableValidator = true;

    protected static $relations = [
        'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category']
    ];
}
