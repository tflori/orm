<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;
use ORM\Relation;

class Category extends Entity
{
    protected static $enableValidator = true;

    protected static $relations = [
        'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category'],
        'articlesAssoc' => [
            Relation::OPT_CARDINALITY => Relation::CARDINALITY_MANY,
            Relation::OPT_CLASS => Article::class,
            Relation::OPT_REFERENCE => ['id' => 'category_id'],
            Relation::OPT_OPPONENT => 'categories',
            Relation::OPT_TABLE => 'article_category',
        ]
    ];
}
