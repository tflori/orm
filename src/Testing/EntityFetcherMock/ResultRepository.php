<?php

namespace ORM\Testing\EntityFetcherMock;

use Mockery\MockInterface;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;

class ResultRepository
{
    const RANDOM_KEY_MIN = 1000000000;
    const RANDOM_KEY_MAX = 1000999999;

    /** @var Entity[][] */
    protected $primaryKeyMap = [];

    /** @var Result[][] */
    protected $results = [];

    /** @var EntityManager */
    protected $em;

    /**
     * ResultRepository constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
     * Add an entity to be fetched by primary key
     *
     * The entity needs to have a primary key if not it will be filled with random values between RANDOM_KEY_MIN and
     * RANDOM_KEY_MAX (at the time writing this it is 1000000000 and 1000999999).
     *
     * You can pass mocks from Entity too but we need to call `Entity::getPrimaryKey()`.
     *
     * @param Entity $entity
     */
    public function addEntity(Entity $entity)
    {
        static::completePrimaryKeys($entity);
        $class = get_class($entity);
        if ($entity instanceof MockInterface) {
            $class = (new \ReflectionClass($entity))->getParentClass()->getName();
        }

        $this->primaryKeyMap[$class][static::buildChecksum($entity->getPrimaryKey())] = $entity;
    }

    /**
     * Retrieve an entity by $primaryKey
     *
     * @param string $class
     * @param array $primaryKey
     * @return Entity|null
     */
    public function retrieve($class, array $primaryKey)
    {
        $checksum = static::buildChecksum($primaryKey);
        return isset($this->primaryKeyMap[$class]) && isset($this->primaryKeyMap[$class][$checksum]) ?
            $this->primaryKeyMap[$class][$checksum] : null;
    }

    /**
     * Create and add a EntityFetcherMock\Result for $class
     *
     * As the results are mocked to come from the database they will also get a primary key if they don't have already.
     *
     * @param $class
     * @param Entity ...$entities
     * @return Result
     */
    public function addResult($class, Entity ...$entities)
    {
        $result = new Result($this->em, $class);
        $result->addEntities(...static::completePrimaryKeys(...$entities));

        if (!isset($this->results[$class])) {
            $this->results[$class] = [spl_object_hash($result) => $result];
        } else {
            $this->results[$class][spl_object_hash($result)] = $result;
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
    public function getResults($class, EntityFetcher $fetcher)
    {
        if (!isset($this->results[$class])) {
            return [];
        }

        $results = [];
        foreach ($this->results[$class] as $objHash => $result) {
            if ($quality = $result->compare($fetcher)) {
                $results[$objHash] = $quality;
            }
        }

        if (empty($results)) {
            return [];
        }

        arsort($results, SORT_DESC);
        $objHash = array_keys($results)[0];
        return $this->results[$class][$objHash]->getEntities();
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
