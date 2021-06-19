<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;
use ORM\Test\Entity\Examples\Concerns\WithTimestamps;

class Article extends Entity implements Taggable
{
    use WithTimestamps;

    protected static $includedAttributes = [
        'intro',
    ];

    protected static $excludedAttributes = [
        'userId',
    ];
    
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category'],
        'writer' => [User::class, ['userId' => 'id']],
        'tags' => [Tag::class, 'parent'],
        'tagsByClass' => [Tag::class, 'parentNoMap'],
        'tagsOverArticleId' => [Tag::class, 'parentDifferentPk'],
        'tagsByArticleId' => [Tag::class, 'parentDifferentFk'],
    ];

    public function getIntro()
    {
        return implode('', array_slice(preg_split(
            '/([\s,\.;\?\!]+)/',
            $this->text,
            61,
            PREG_SPLIT_DELIM_CAPTURE
        ), 0, 59));
    }
}
