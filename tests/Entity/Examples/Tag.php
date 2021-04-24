<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class Tag extends Entity
{
    protected static $relations = [
        'parent' => [['parentType' => [
            'image' => Image::class,
            'article' => Article::class,
        ]], ['parentId' => 'id']],
        'parentNoMap' => [['parentType' => Taggable::class], ['parentId' => 'id']],
        'parentDifferentPk' => [
            ['parentType' => [
                'image' => Image::class,
                'article' => Article::class,
            ]],
            ['parentId' => [
                'image' => 'imageId',
                'article' => 'articleId',
            ]],
        ],
        'parentDifferentFk' => [
            ['parentType' => [
                'image' => Image::class,
                'article' => Article::class,
            ]],
            [
                'image' => ['imageId' => 'id'],
                'article' => ['articleId' => 'id'],
            ],
        ],
    ];
}
