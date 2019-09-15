<?php

namespace ORM\Testing;

use Mockery as m;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\NoEntity;
use ORM\Testing\EntityFetcherMock\Result;
use ORM\Testing\EntityFetcherMock\ResultRepository;

class EntityManagerMock extends EntityManager
{
    protected $resultRepository;

    public function __construct($options = [])
    {
        static::$emMapping['byClass'] = [];
        parent::__construct($options);
        $this->resultRepository = new ResultRepository($this);
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
     * @codeCoverageIgnore proxy method
     */
    public function addEntity(Entity $entity)
    {
        $this->resultRepository->addEntity($entity);
    }

    /**
     * Retrieve an entity by $primaryKey
     *
     * @param string $class
     * @param array $primaryKey
     * @return Entity|null
     * @codeCoverageIgnore proxy method
     */
    public function retrieve($class, array $primaryKey)
    {
        return $this->resultRepository->retrieve($class, $primaryKey);
    }

    /**
     * Create and add a EntityFetcherMock\Result for $class
     *
     * As the results are mocked to come from the database they will also get a primary key if they don't have already.
     *
     * @param $class
     * @param Entity ...$entities
     * @return Result|m\MockInterface
     * @codeCoverageIgnore proxy method
     */
    public function addResult($class, Entity ...$entities)
    {
        return $this->resultRepository->addResult($class, ...$entities);
    }

    /**
     * Get the results for $class and $query
     *
     * The EntityFetcherMock\Result gets a quality for matching this query. Only the highest quality will be used.
     *
     * @param string $class
     * @param EntityFetcher $fetcher
     * @return array
     * @codeCoverageIgnore proxy method
     */
    public function getResults($class, EntityFetcher $fetcher)
    {
        return $this->resultRepository->getResults($class, $fetcher);
    }

    /** {@inheritDoc} */
    public function fetch($class, $primaryKey = null)
    {
        $reflection = new \ReflectionClass($class);
        if (!$reflection->isSubclassOf(Entity::class)) {
            throw new NoEntity($class . ' is not a subclass of Entity');
        }
        /** @var string|Entity $class */

        if ($primaryKey === null) {
            return new EntityFetcherMock($this, $class);
        }

        $primaryKey = $this->buildPrimaryKey($class, (array)$primaryKey);
        $checksum = $this->buildChecksum($primaryKey);

        if (isset($this->map[$class][$checksum])) {
            return $this->map[$class][$checksum];
        }

        return $this->retrieve($class, $primaryKey);
    }
}
