<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityManager;

class OneToOne extends OneToMany
{
    /** {@inheritdoc} */
    public function fetch(Entity $me, EntityManager $entityManager = null)
    {
        return parent::fetch($me, $entityManager)->one();
    }
}
