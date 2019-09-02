<?php

namespace ORM\Testing;

use Mockery\MockInterface;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Testing\EntityFetcherMock\Result;

class EntityFetcherMock extends EntityFetcher
{
    const RANDOM_KEY_MIN = 1000000000;
    const RANDOM_KEY_MAX = 1000999999;

    /** @var Entity[][]  */
    protected static $primaryKeyMap = [];

    /** @var Result[][] */
    protected static $results = [];

    /** @var array */
    protected $currentResult;

    /**
     * Add an entity to be fetched by primary key
     *
     * The entity needs to have a primary key if not it will be filled with random values between RANDOM_KEY_MIN and
     * RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).
     *
     * You can pass mocks from Entity too but we need to call `Entity::getPrimaryKey()`.
     *
     * @param Entity $entity
     */
    public static function addEntity(Entity $entity)
    {
        static::completePrimaryKeys($entity);
        $class = get_class($entity);
        if ($entity instanceof MockInterface) {
            $class = (new \ReflectionClass($entity))->getParentClass()->getName();
        }

        static::$primaryKeyMap[$class][static::buildChecksum($entity->getPrimaryKey())] = $entity;
    }

    /**
     * Retrieve an entity by $primaryKey
     *
     * @param string $class
     * @param array $primaryKey
     * @return Entity|null
     */
    public static function retrieve($class, array $primaryKey)
    {
        $checksum = static::buildChecksum($primaryKey);
        return isset(static::$primaryKeyMap[$class]) && isset(static::$primaryKeyMap[$class][$checksum]) ?
            static::$primaryKeyMap[$class][$checksum] : null;
    }

    /**
     * Create and add a EntityFetcherMock\Result for $class
     *
     * As the results are mocked to come from the database they will also get a primary key if they don't have already.
     *
     * @param $class
     * @param EntityManager $em
     * @param Entity ...$entities
     * @return Result
     */
    public static function addResult($class, EntityManager $em, Entity ...$entities)
    {
        $result = new Result($em, $class);
        $result->addEntities(...static::completePrimaryKeys(...$entities));

        if (!isset(static::$results[$class])) {
            static::$results[$class] = [spl_object_hash($result) => $result];
        } else {
            static::$results[$class][spl_object_hash($result)] = $result;
        }

        return $result;
    }

    /**
     * Get the results for $class and $query
     *
     * The EntityFetcherMock\Result gets a quality for matching this query. Only the highest quality will be used.
     *
     * @param string $class
     * @param EntityFetcher $fetcher
     * @return array
     */
    public static function getResults($class, EntityFetcher $fetcher)
    {
        if (!isset(static::$results[$class])) {
            return [];
        }

        $results = [];
        foreach (static::$results[$class] as $objHash => $result) {
            if ($quality = $result->compare($fetcher)) {
                $results[$objHash] = $quality;
            }
        }

        if (empty($results)) {
            return [];
        }

        arsort($results, SORT_DESC);
        $objHash = array_keys($results)[0];
        return static::$results[$class][$objHash]->getEntities();
    }

    /**
     * Fill the primary keys of $entities
     *
     * If the primary key is incomplete the missing attributes will be filled with a random integer between
     * RANDOM_KEY_MIN and RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).
     *
     * @param Entity ...$entities
     * @return Entity[]
     */
    public static function completePrimaryKeys(Entity ...$entities)
    {
        // complete the primary keys with random numbers
        foreach ($entities as $entity) {
            foreach ($entity::getPrimaryKeyVars() as $attribute) {
                if ($entity->$attribute === null) {
                    $entity->$attribute = mt_rand(static::RANDOM_KEY_MIN, static::RANDOM_KEY_MAX);
                }
            }
        }

        return $entities;
    }

    /**
     * Reset the statics from
     */
    public static function reset()
    {
        static::$primaryKeyMap = [];
        static::$results = [];
    }

    /** {@inheritDoc} */
    public function one()
    {
        if ($this->currentResult === null) {
            $this->currentResult = static::getResults($this->class, $this);
        }

        return array_shift($this->currentResult);
    }

    /** {@inheritDoc} */
    public function count()
    {
        return count(static::getResults($this->class, $this));
    }

    /**
     * Build a checksum from $primaryKey
     *
     * @param array $primaryKey
     * @return string
     */
    protected static function buildChecksum(array $primaryKey)
    {
        return md5(serialize($primaryKey));
    }
}
