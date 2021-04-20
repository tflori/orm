<?php

namespace ORM;

use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidRelation;
use ORM\Relation\ManyToMany;
use ORM\Relation\Morphed;
use ORM\Relation\OneToMany;
use ORM\Relation\OneToOne;
use ORM\Relation\Owner;

/**
 * Base Relation
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Relation
{
    const OPT_CLASS        = 'class';
    const OPT_MORPH_COLUMN = 'morphColumn';
    const OPT_MORPH        = 'morph';
    const OPT_REFERENCE    = 'reference';
    const OPT_CARDINALITY  = 'cardinality';
    const OPT_OPPONENT     = 'opponent';
    const OPT_TABLE        = 'table';
    const OPT_FILTERS      = 'filters';
    const CARDINALITY_ONE  = 'one';
    const CARDINALITY_MANY = 'many';

    /** The parent entity that defined this relation
     * @var string */
    protected $parent;

    /** The name of the relation for error messages
     * @var string */
    protected $name;

    /** The class that is related
     * @var string */
    protected $class;

    /** The name of the relation in the related class
     * @var string */
    protected $opponent;

    /** Reference definition as key value pairs
     * @var array */
    protected $reference;

    /** Filters applied to all fetchers
     * @var array */
    protected $filters = [];

    /**
     * Factory for relation definition object
     *
     * @param string $parent
     * @param string $name
     * @param array $relDef
     * @return Relation
     * @throws InvalidConfiguration
     */
    public static function createRelation($parent, $name, $relDef)
    {
        if (isset($relDef[0])) {
            $relationClasses = [
                Owner::class,
                ManyToMany::class,
                OneToMany::class,
                OneToOne::class,
                Morphed::class,
            ];
            /** @var string|Relation $relationClass */
            foreach ($relationClasses as $relationClass) {
                if ($relation = $relationClass::fromShort($relDef)) {
                    return $relation;
                }
            }
            throw new InvalidConfiguration('Invalid short form for relation ' . $name . ' for entity ' . $parent);
        }

        return self::fromAssoc($parent, $name, $relDef);
    }

    /**
     * Creates a relation from short form
     *
     * @param array $short
     * @internal
     * @see Relation::createRelation()
     * @return ?Relation
     * @codeCoverageIgnore
     */
    public static function fromShort(array $short)
    {
        return null;
    }

    /**
     * Create a relation by assoc definition
     *
     * @param string $parent
     * @param string $name
     * @param array $relDef
     * @internal
     * @see Relation::createRelation()
     * @return Relation
     * @throws InvalidConfiguration
     */
    protected static function fromAssoc($parent, $name, array $relDef)
    {
        $class       = isset($relDef[self::OPT_CLASS]) ? $relDef[self::OPT_CLASS] : null;
        $morphColumn = isset($relDef[self::OPT_MORPH_COLUMN]) ? $relDef[self::OPT_MORPH_COLUMN] : null;
        $morph       = isset($relDef[self::OPT_MORPH]) ? $relDef[self::OPT_MORPH] : null;
        $reference   = isset($relDef[self::OPT_REFERENCE]) ? $relDef[self::OPT_REFERENCE] : null;
        $table       = isset($relDef[self::OPT_TABLE]) ? $relDef[self::OPT_TABLE] : null;
        $opponent    = isset($relDef[self::OPT_OPPONENT]) ? $relDef[self::OPT_OPPONENT] : null;
        $cardinality = isset($relDef[self::OPT_CARDINALITY]) ? $relDef[self::OPT_CARDINALITY] : null;
        $filters     = isset($relDef[self::OPT_FILTERS]) ? $relDef[self::OPT_FILTERS] : [];

        // create instances from filter classes
        $filters = array_map([static::class, 'createFilter'], $filters);

        if (!$class && (!$morphColumn || !$morph) || !$reference && !$opponent) {
            throw new InvalidConfiguration('Invalid relation ' . $name . ' for entity ' . $parent);
        }

        if ($reference && $morphColumn && $morph) {
            return new Morphed($morphColumn, $morph, $reference);
        } elseif ($reference && !isset($table)) {
            return new Owner($class, $reference);
        } elseif ($table) {
            return new ManyToMany($class, $reference, $opponent, $table, $filters);
        } elseif (!$cardinality || $cardinality === self::CARDINALITY_MANY) {
            return new OneToMany($class, $opponent, $filters);
        } else {
            return new OneToOne($class, $opponent, $filters);
        }
    }

    /**
     * Create an instance from $class
     *
     * @param string|mixed $class
     * @return mixed
     */
    protected static function createFilter($class)
    {
        if (is_string($class) && class_exists($class)) {
            return new $class;
        }
        return $class;
    }

    /**
     * Bind this relation to class $parent with $name
     *
     * @param string $parent
     * @param string $name
     */
    public function bind($parent, $name)
    {
        if ($this->name || $this->parent) {
            throw new \LogicException('Method ' . __METHOD__ . ' should only be called once');
        }
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return Owner|ManyToMany
     */
    public function getOpponent()
    {
        return call_user_func([ $this->class, 'getRelation' ], $this->opponent);
    }

    /**
     * Fetch the relation
     *
     * Runs fetch on the EntityManager and returns its result.
     *
     * @param Entity        $self
     * @param EntityManager $entityManager
     * @return mixed
     */
    abstract public function fetch(Entity $self, EntityManager $entityManager);

    /**
     * Fetch all from the relation
     *
     * Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.
     *
     * @param Entity        $self
     * @param EntityManager $entityManager
     * @return Entity[]|Entity
     */
    public function fetchAll(Entity $self, EntityManager $entityManager)
    {
        $fetcher = $this->fetch($self, $entityManager);

        if ($fetcher instanceof EntityFetcher) {
            return $fetcher->all();
        }

        return $fetcher;
    }

    /**
     * Get the foreign key for the given reference
     *
     * @param Entity $self
     * @param array $reference
     * @return array
     * @throws IncompletePrimaryKey
     */
    protected function getForeignKey(Entity $self, $reference)
    {
        $foreignKey = [];

        foreach ($reference as $attribute => $fkAttribute) {
            $foreignKey[$fkAttribute] = $self->getAttribute($attribute);

            if ($foreignKey[$fkAttribute] === null) {
                throw new IncompletePrimaryKey('Key incomplete for join');
            }
        }

        return $foreignKey;
    }

    /**
     * Set the relation to $entity
     *
     * @param Entity $self
     * @param Entity|null $entity
     * @throws InvalidRelation
     */
    public function setRelated(Entity $self, Entity $entity = null)
    {
        throw new InvalidRelation('This is not the owner of the relation');
    }

    /**
     * Add $entities to association table
     *
     * @param Entity $self
     * @param Entity[] $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function addRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        throw new InvalidRelation('This is not a many-to-many relation');
    }

    /**
     * Delete $entities from association table
     *
     * @param Entity $self
     * @param Entity[] $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function deleteRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        throw new InvalidRelation('This is not a many-to-many relation');
    }

    /**
     * Join this relation in $fetcher
     *
     * @param EntityFetcher $fetcher
     * @param string        $join
     * @param string        $alias
     * @internal Used only from EntityFetcher
     * @return mixed
     */
    abstract public function addJoin(EntityFetcher $fetcher, $join, $alias);
}
