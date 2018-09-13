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
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        return parent::fetch($self, $entityManager)->one();
    }
}
