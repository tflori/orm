<?php

namespace ORM;

use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidRelation;
use ORM\Relation\ManyToMany;
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
    const OPT_REFERENCE    = 'reference';
    const OPT_CARDINALITY  = 'cardinality';
    const OPT_OPPONENT     = 'opponent';
    const OPT_TABLE        = 'table';
    const OPT_FILTERS      = 'filters';
    const CARDINALITY_ONE  = 'one';
    const CARDINALITY_MANY = 'many';

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
     * @param string $name
     * @param array $relDef
     * @return Relation
     */
    public static function createRelation($name, $relDef)
    {
        if (isset($relDef[0])) {
            $relDef = self::convertShort($name, $relDef);
        }

        $class       = isset($relDef[self::OPT_CLASS]) ? $relDef[self::OPT_CLASS] : null;
        $reference   = isset($relDef[self::OPT_REFERENCE]) ? $relDef[self::OPT_REFERENCE] : null;
        $table       = isset($relDef[self::OPT_TABLE]) ? $relDef[self::OPT_TABLE] : null;
        $opponent    = isset($relDef[self::OPT_OPPONENT]) ? $relDef[self::OPT_OPPONENT] : null;
        $cardinality = isset($relDef[self::OPT_CARDINALITY]) ? $relDef[self::OPT_CARDINALITY] : null;
        $filters     = isset($relDef[self::OPT_FILTERS]) ? $relDef[self::OPT_FILTERS] : [];

        // create instances from filter classes
        $filters = array_map([static::class, 'createFilter'], $filters);

        if ($reference && !isset($table)) {
            return new Owner($name, $class, $reference);
        } elseif ($table) {
            return new ManyToMany($name, $class, $reference, $opponent, $table, $filters);
        } elseif (!$cardinality || $cardinality === self::CARDINALITY_MANY) {
            return new OneToMany($name, $class, $opponent, $filters);
        } else {
            return new OneToOne($name, $class, $opponent, $filters);
        }
    }

    /**
     * Converts short form to assoc form
     *
     * @param string $name
     * @param array $short
     * @return array
     * @throws InvalidConfiguration
     */
    protected static function convertShort($name, $short)
    {
        $length = count($short);
        $cardinality = in_array($short[0], [self::CARDINALITY_ONE, self::CARDINALITY_MANY]) ?
            array_shift($short) : self::CARDINALITY_MANY;
        $class = array_shift($short);

        if (is_array($short[0])) {
            $reference = array_shift($short);


            if ($cardinality === self::CARDINALITY_ONE || $length === 2) {
                return [
                    self::OPT_CARDINALITY => self::CARDINALITY_ONE,
                    self::OPT_CLASS       => $class,
                    self::OPT_REFERENCE   => $reference,
                ];
            }
        }

        $opponent = array_shift($short);

        if (isset($reference)) {
            if (!isset($short[0]) || !is_string($short[0])) {
                throw new InvalidConfiguration('Invalid short form for relation ' . $name);
            }
            $table = array_shift($short);
            $relDef = [
                self::OPT_CARDINALITY => self::CARDINALITY_MANY,
                self::OPT_CLASS       => $class,
                self::OPT_REFERENCE   => $reference,
                self::OPT_OPPONENT    => $opponent,
                self::OPT_TABLE       => $table,
            ];
        } else {
            $relDef = [
                self::OPT_CARDINALITY => $cardinality,
                self::OPT_CLASS       => $class,
                self::OPT_OPPONENT    => $opponent,
            ];
        }

        if (isset($short[0]) && is_array($short[0])) {
            $relDef[self::OPT_FILTERS] = array_shift($short);
        }

        if (!empty($short)) {
            throw new InvalidConfiguration('Invalid short form for relation ' . $name);
        }

        return $relDef;
    }

    protected static function createFilter($class)
    {
        if (is_string($class) && class_exists($class)) {
            return new $class;
        }
        return $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
     * @return mixed
     */
    abstract public function addJoin(EntityFetcher $fetcher, $join, $alias);
}
