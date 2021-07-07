<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Helper;

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
    public static function fromShort($parent, array $short)
    {
        // the cardinality is mandatory for one to one
        if ($short[0] !== self::CARDINALITY_ONE) {
            return null;
        }
        array_shift($short);

        return static::createStaticFromShort($short);
    }

    /** {@inheritDoc} */
    protected static function fromAssoc($parent, array $relDef)
    {
        if (!isset($relDef[self::OPT_CARDINALITY]) || $relDef[self::OPT_CARDINALITY] === self::CARDINALITY_MANY) {
            return null;
        }

        return self::createStaticFromAssoc($relDef);
    }

    /** {@inheritDoc} */
    public function eagerLoad(EntityManager $em, Entity ...$entities)
    {
        $foreignObjects = $this->getOpponent(Owner::class)->eagerLoadSelf($em, ...$entities);
        foreach ($entities as $entity) {
            $key = spl_object_hash($entity);
            $entity->setCurrentRelated(
                $this->name,
                isset($foreignObjects[$key]) ? Helper::first($foreignObjects[$key]) : null
            );
        }
    }
}
