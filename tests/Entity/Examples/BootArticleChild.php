<?php

namespace ORM\Test\Entity\Examples;

class BootArticleChild extends BootArticle
{
    protected static function boot()
    {
        // inherits the relations from BootArticle::boot()
        parent::boot();
    }
}
