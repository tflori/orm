<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\Helper;

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

    /**
     * Set all children and return an array containing the root elements
     *
     * This method expects you to pass all elements of a branch. Keep in mind that that the array you
     * are getting is not necessarily from the same parent if you are not passing all elements of a branch.
     *
     * Example of this issue:
     * Elements passed `['1.', '1.2.', '1.3.', '3.5.', '4.']` (each element is represented by its materialized path).
     * As '3.' is not passed, '3.5.' seems to be the root element resulting in the following tree:
     * ```php
     * [
     *   [path => '1.', children => [['path' => '1.2.'],['path' => '1.3.']]],
     *   [path => '3.5.'],
     *   [path => '4.'],
     * ]
     * ```
     *
     * Example usage when you are using materialized paths:
     * ```php
     * $children = $em->fetch('Category::class')->where('path', 'LIKE', '3.5._%')->all();
     * $treeOf35 = Category::getRelation('children')->buildTree(...$children);
     * ```
     *
     * @param Entity ...$entities
     * @return Entity[]
     */
    public function buildTree(Entity ...$entities)
    {
        $reference = $this->getOpponent(Owner::class)->getReference();
        /** @var Entity[] $entities */
        $entities = Helper::keyBy($entities, array_values($reference));
        $tree = [];
        foreach (Helper::groupBy($entities, array_keys($reference)) as $key => $children) {
            if (!isset($entities[$key])) {
                $tree = array_merge($tree, $children);
                continue;
            }
            $entities[$key]->setCurrentRelated($this->name, $children);
        }
        return $tree;
    }
}
