<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityManager;

/**
 * OneToOne Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class OneToOne extends OneToMany
{
    /** {@inheritdoc} */
    public function fetch(Entity $me, EntityManager $entityManager)
    {
        return parent::fetch($me, $entityManager)->one();
    }
}
