<?php

namespace ORM;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidRelation;
use ORM\Relation\ManyToMany;
use ORM\Relation\OneToMany;
use ORM\Relation\OneToOne;
use ORM\Relation\Owner;

abstract class Relation
{
    const CARDINALITY_ONE          = 'one';
    const CARDINALITY_MANY         = 'many';

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
     * @param array $relDef
     * @return Relation
     */
    public static function createRelation($name, $relDef)
    {
        if (isset($relDef[0])) {
            $relDef = self::convertShort($name, $relDef);
        }

        if (isset($relDef[Entity::OPT_RELATION_REFERENCE]) && !isset($relDef[Entity::OPT_RELATION_TABLE])) {
            return new Owner(
                $name,
                $relDef[Entity::OPT_RELATION_CLASS],
                $relDef[Entity::OPT_RELATION_REFERENCE]
            );
        } elseif (isset($relDef[Entity::OPT_RELATION_TABLE])) {
            return new ManyToMany(
                $name,
                $relDef[Entity::OPT_RELATION_CLASS],
                $relDef[Entity::OPT_RELATION_REFERENCE],
                $relDef[Entity::OPT_RELATION_OPPONENT],
                $relDef[Entity::OPT_RELATION_TABLE]
            );
        } elseif (!isset($relDef[Entity::OPT_RELATION_CARDINALITY]) ||
                  $relDef[Entity::OPT_RELATION_CARDINALITY] === self::CARDINALITY_MANY
        ) {
            return new OneToMany(
                $name,
                $relDef[Entity::OPT_RELATION_CLASS],
                $relDef[Entity::OPT_RELATION_OPPONENT]
            );
        } else {
            return new OneToOne(
                $name,
                $relDef[Entity::OPT_RELATION_CLASS],
                $relDef[Entity::OPT_RELATION_OPPONENT]
            );
        }
    }

    /**
     * Converts short form to assoc form
     *
     * @param string $name
     * @param string $relDef
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
                Entity::OPT_RELATION_CARDINALITY => self::CARDINALITY_ONE,
                Entity::OPT_RELATION_CLASS       => $relDef[0],
                Entity::OPT_RELATION_REFERENCE   => $relDef[1],
            ];
        } elseif ($length === 3 && $relDef[0] === self::CARDINALITY_ONE) {
            // non-owner of one-to-one
            return [
                Entity::OPT_RELATION_CARDINALITY => self::CARDINALITY_ONE,
                Entity::OPT_RELATION_CLASS       => $relDef[1],
                Entity::OPT_RELATION_OPPONENT    => $relDef[2],
            ];
        } elseif ($length === 2) {
            // non-owner of one-to-many
            return [
                Entity::OPT_RELATION_CARDINALITY => self::CARDINALITY_MANY,
                Entity::OPT_RELATION_CLASS       => $relDef[0],
                Entity::OPT_RELATION_OPPONENT    => $relDef[1],
            ];
        } elseif ($length === 4 && gettype($relDef[1]) === 'array') {
            // many-to-many
            return [
                Entity::OPT_RELATION_CARDINALITY => self::CARDINALITY_MANY,
                Entity::OPT_RELATION_CLASS       => $relDef[0],
                Entity::OPT_RELATION_REFERENCE   => $relDef[1],
                Entity::OPT_RELATION_OPPONENT    => $relDef[2],
                Entity::OPT_RELATION_TABLE       => $relDef[3],
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

        return call_user_func([$this->class, 'getRelation'], $this->opponent);
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
     * @return array
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

        foreach ($reference as $var => $fkCol) {
            $value = $me->__get($var);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete for join');
            }

            $foreignKey[$fkCol] = $value;
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
     * @return mixed
     */
    abstract public function addJoin(EntityFetcher $fetcher, $join);
}
