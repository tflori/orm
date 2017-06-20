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

    /**
     * Factory for relation definition object
     *
     * @param string $name
     * @param array  $relDef
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
        $cardinality = isset($relDef[self::OPT_CARDINALITY]) ?
            $relDef[self::OPT_CARDINALITY] : null;

        if ($reference && !isset($table)) {
            return new Owner($name, $class, $reference);
        } elseif ($table) {
            return new ManyToMany($name, $class, $reference, $opponent, $table);
        } elseif (!$cardinality || $cardinality === self::CARDINALITY_MANY) {
            return new OneToMany($name, $class, $opponent);
        } else {
            return new OneToOne($name, $class, $opponent);
        }
    }

    /**
     * Converts short form to assoc form
     *
     * @param string $name
     * @param array  $relDef
     * @return array
     * @throws InvalidConfiguration
     */
    protected static function convertShort($name, $relDef)
    {
        // convert the short form
        $length = count($relDef);

        if ($length === 2 && gettype($relDef[1]) === 'array') {
            // owner of one-to-many or one-to-one
            return [
                self::OPT_CARDINALITY => self::CARDINALITY_ONE,
                self::OPT_CLASS       => $relDef[0],
                self::OPT_REFERENCE   => $relDef[1],
            ];
        } elseif ($length === 3 && $relDef[0] === self::CARDINALITY_ONE) {
            // non-owner of one-to-one
            return [
                self::OPT_CARDINALITY => self::CARDINALITY_ONE,
                self::OPT_CLASS       => $relDef[1],
                self::OPT_OPPONENT    => $relDef[2],
            ];
        } elseif ($length === 2) {
            // non-owner of one-to-many
            return [
                self::OPT_CARDINALITY => self::CARDINALITY_MANY,
                self::OPT_CLASS       => $relDef[0],
                self::OPT_OPPONENT    => $relDef[1],
            ];
        } elseif ($length === 4 && gettype($relDef[1]) === 'array') {
            // many-to-many
            return [
                self::OPT_CARDINALITY => self::CARDINALITY_MANY,
                self::OPT_CLASS       => $relDef[0],
                self::OPT_REFERENCE   => $relDef[1],
                self::OPT_OPPONENT    => $relDef[2],
                self::OPT_TABLE       => $relDef[3],
            ];
        } else {
            throw new InvalidConfiguration('Invalid short form for relation ' . $name);
        }
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
     * @return Relation
     */
    public function getOpponent()
    {
        // @todo seems we need a test for it
//        if (!$this->opponent) {
//            return null;
//        }

        return call_user_func([ $this->class, 'getRelation' ], $this->opponent);
    }

    /**
     * Fetch the relation
     *
     * Runs fetch on the EntityManager and returns its result.
     *
     * @param Entity        $me
     * @param EntityManager $entityManager
     * @return mixed
     */
    abstract public function fetch(Entity $me, EntityManager $entityManager);

    /**
     * Fetch all from the relation
     *
     * Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.
     *
     * @param Entity        $me
     * @param EntityManager $entityManager
     * @return Entity[]|Entity
     */
    public function fetchAll(Entity $me, EntityManager $entityManager)
    {
        $fetcher = $this->fetch($me, $entityManager);

        if ($fetcher instanceof EntityFetcher) {
            return $fetcher->all();
        }

        return $fetcher;
    }

    /**
     * Get the foreign key for the given reference
     *
     * @param Entity $me
     * @param array  $reference
     * @return array
     * @throws IncompletePrimaryKey
     */
    protected function getForeignKey(Entity $me, $reference)
    {
        $foreignKey = [];

        foreach ($reference as $attribute => $fkAttribute) {
            $value = $me->__get($attribute);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete for join');
            }

            $foreignKey[$fkAttribute] = $value;
        }

        return $foreignKey;
    }

    /**
     * Set the relation to $entity
     *
     * @param Entity      $me
     * @param Entity|null $entity
     * @throws InvalidRelation
     */
    public function setRelated(Entity $me, Entity $entity = null)
    {
        throw new InvalidRelation('This is not the owner of the relation');
    }

    /**
     * Add $entities to association table
     *
     * @param Entity        $me
     * @param Entity[]      $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function addRelated(Entity $me, array $entities, EntityManager $entityManager)
    {
        throw new InvalidRelation('This is not a many-to-many relation');
    }

    /**
     * Delete $entities from association table
     *
     * @param Entity        $me
     * @param Entity[]      $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function deleteRelated(Entity $me, array $entities, EntityManager $entityManager)
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
