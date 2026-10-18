<?php

namespace ORM\Test\Entity\Examples;

class BootWithoutParentBoot extends BootArticle
{
    protected static function boot()
    {
        // does not call parent::boot() - relations of BootArticle::boot() and traits are not booted
        static::defineRelation('source', [User::class, ['sourceId' => 'id']]);
    }
}
