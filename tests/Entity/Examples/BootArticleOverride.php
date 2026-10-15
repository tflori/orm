<?php

namespace ORM\Test\Entity\Examples;

class BootArticleOverride extends BootArticle
{
    protected static function boot()
    {
        parent::boot();
        static::defineRelation('writer', [Category::class, ['categoryId' => 'id']]);
    }
}
