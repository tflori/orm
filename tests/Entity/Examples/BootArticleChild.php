<?php

namespace ORM\Test\Entity\Examples;

class BootArticleChild extends BootArticle
{
    protected static function boot()
    {
        // does not call parent::boot() - relations from BootArticle::boot() have to be inherited anyway
        static::bootTraits();
    }
}
