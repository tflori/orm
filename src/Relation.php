<?php

namespace ORM;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidRelation;

abstract class Relation
{
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
