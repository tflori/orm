<?php

namespace ORM\Relation;

/**
 * A parent to children (many)
 */
class ParentChildren extends OneToMany
{
    /** {@inheritDoc} */
    public static function fromShort($parent, array $short)
    {
        if ($short[0] === self::CARDINALITY_ONE) {
            return null;
        } elseif ($short[0] === self::CARDINALITY_MANY) {
            array_shift($short);
        }

        if ($parent !== $short[0]) {
            return null;
        }

        return static::createStaticFromShort($short);
    }

    /** {@inheritDoc} */
    protected static function fromAssoc($parent, array $relDef)
    {
        if (!isset($relDef[self::OPT_CLASS]) || $parent !== $relDef[self::OPT_CLASS]) {
            return null;
        }

        return parent::fromAssoc($parent, $relDef);
    }
}
