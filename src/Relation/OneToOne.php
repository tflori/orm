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

    /** {@inheritDoc} */
    public static function fromShort(array $short)
    {
        // the cardinality is mandatory for one to one
        if ($short[0] !== self::CARDINALITY_ONE) {
            return null;
        }
        array_shift($short);

        return static::createStaticFromShort($short);
    }
}
