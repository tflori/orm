<?php

namespace ORM\Testing;

use Mockery as m;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use PDO;

/**
 * A trait to mock ORM\EntityManager
 *
 * @package ORM\Testing
 * @author  Thomas Flori <thflori@gmail.com>
 */
trait MocksEntityManager
{
    /**
     * Get the EntityManagerMock for $class
     *
     * @param $class
     * @return EntityManagerMock|m\MockInterface|EntityManager
     * @codeCoverageIgnore proxy method
     */
    public function ormGetEntityManagerInstance($class)
    {
        return EntityManager::getInstance($class);
    }

    /**
     * Convert an array with $attributes as keys to an array of columns for $class
     *
     * e. g. : `assertSame(['first_name' => 'John'], ormAttributesToArray(User::class, ['firstName' => 'John'])`
     *
     * *Note: this method is idempotent*
     *
     * @param string $class
     * @param array  $attributes
     * @return array
     */
    public function ormAttributesToData($class, array $attributes)
    {
        $data = [];

        foreach ($attributes as $attribute => $value) {
            $data[call_user_func([$class, 'getColumnName'], $attribute)] = $value;
        }

        return $data;
    }

    /**
     * Create a partial mock of Entity $class
     *
     * *Note: the entity will get a random primary key if not predefined.*
     *
     * @param string        $class
     * @param array         $data
     * @return m\MockInterface|Entity
     */
    public function ormCreateMockedEntity($class, $data = [])
    {
        /** @var EntityManagerMock $em */
        $em = $this->ormGetEntityManagerInstance($class);

        /** @var Entity|m\MockInterface $entity */
        $entity = m::mock($class)->makePartial();
        $entity->shouldReceive('validate')->andReturn(true)->byDefault();
        $entity->setEntityManager($em);
        $entity->setOriginalData($this->ormAttributesToData($class, $data));
        $entity->reset();

        $em->addEntity($entity);
        return $entity;
    }

    /**
     * Initialize an EntityManager mock object
     *
     * The mock is partial and you can map and act with it as usual. You should overwrite your dependency injector
     * with the returned mock object. You can also call `defineFor*()` on this mock to use this mock for specific
     * classes.
     *
     * The PDO object is mocked too. This object should not receive any calls except for quoting. By default it
     * accepts `quote(string)`, `setAttribute(*)` and `getAttribute(ATTR_DRIVER_NAME)`. To retrieve and expect other
     * calls you can use `getConnection()` from EntityManager mock object.
     *
     * @param array $options Options passed to EntityManager constructor
     * @param string $driver Database driver you are using (results in different dbal instance)
     * @return m\MockInterface|EntityManager
     */
    public function ormInitMock($options = [], $driver = 'mysql')
    {
        /** @var EntityManager|m\MockInterface $em */
        $em = m::mock(EntityManagerMock::class)->makePartial();
        $em->__construct($options);

        /** @var PDO|m\Mock $pdo */
        $pdo = m::mock(PDO::class);
        $pdo->shouldReceive('setAttribute')->andReturn(true)->byDefault();
        $pdo->shouldReceive('getAttribute')->with(PDO::ATTR_DRIVER_NAME)->andReturn($driver)->byDefault();
        $pdo->shouldReceive('quote')->with(m::type('string'))->andReturnUsing(
            function ($str) {
                return '\'' . addcslashes($str, '\'') . '\'';
            }
        )->byDefault();
        $em->setConnection($pdo);

        return $em;
    }

    /**
     * Add a result to EntityFetcher for $class
     *
     * You can specify the query that you expect in the returned result.
     *
     * Example:
     * ```php
     * $this->ormAddResult(Article::class, $em, new Article(['title' => 'Foo']))
     *   ->where('deleted_at IS NULL')
     *   ->where('title', 'Foo');
     *
     * $entity = $em->fetch('Article::class')
     *   ->where('deleted_at IS NULL')
     *   ->where('title', 'Foo')
     *   ->one();
     * ```
     *
     * @param string $class The class of an Entity
     * @param Entity ...$entities The entities that will be returned
     * @return EntityFetcherMock\Result|m\MockInterface
     * @codeCoverageIgnore trivial code
     */
    public function ormAddResult($class, Entity ...$entities)
    {
        /** @var EntityManagerMock|m\Mock $em */
        $em = $this->ormGetEntityManagerInstance($class);
        return $em->addResult($class, ...$entities);
    }

    /**
     * Expect fetch for $class
     *
     * Mocks and expects an EntityFetcher with $entities as result.
     *
     * @param string        $class    The class that should be fetched
     * @param array         $entities The entities that get returned from fetcher
     * @return m\Mock|EntityFetcher
     * @deprecated use $em->shouldReceive('fetch')->once()->passthru()
     */
    public function ormExpectFetch($class, $entities = [])
    {
        /** @var m\Mock|EntityFetcher $fetcher */
        list($expectation, $fetcher) = $this->ormAllowFetch($class, $entities);
        $expectation->once();
        return $fetcher;
    }

    /**
     * Allow fetch for $class
     *
     * Mocks an EntityFetcher with $entities as result.
     *
     * Returns the Expectation for fetch on entityManager and the mocked EntityFetcher
     *
     * @param string        $class    The class that should be fetched
     * @param array         $entities The entities that get returned from fetcher
     * @return m\Expectation[]|EntityFetcher[]|m\MockInterface[]
     * @deprecated every fetch is allowed now (change with $em->shouldNotReceive('fetch'))
     */
    public function ormAllowFetch($class, $entities = [])
    {
        /** @var EntityManager|m\Mock $em */
        $em = $this->ormGetEntityManagerInstance($class);

        /** @var m\MockInterface|EntityFetcher $fetcher */
        $fetcher = m::mock(EntityFetcher::class, [ $em, $class ])->makePartial();
        $expectation = $em->shouldReceive('fetch')->with($class)->andReturn($fetcher);

        $fetcher->shouldReceive('count')->with()->andReturn(count($entities))->byDefault();
        array_push($entities, null);
        $fetcher->shouldReceive('one')->with()->andReturnValues($entities)->byDefault();

        return [$expectation, $fetcher];
    }

    /**
     * Expect an insert for $class
     *
     * Mocks and expects the calls to sync and insert as they came for `save()` method for a new Entity.
     *
     * If you omit the auto incremented id in defaultValues it is set to a random value between 1 and 2147483647.
     *
     * The EntityManager gets determined the same way as in Entity and can be overwritten by third parameter here.
     *
     * @param string        $class         The class that should get created
     * @param array         $defaultValues The default values that came from database (for example: the created column
     *                                     has by the default the current timestamp; the id is auto incremented...)
     */
    public function ormExpectInsert($class, $defaultValues = [])
    {
        $expectation = $this->ormAllowInsert($class, $defaultValues);
        $expectation->once();
    }

    /**
     * Allow an insert for $class
     *
     * Mocks the calls to sync and insert as they came for `save()` method for a new Entity.
     *
     * If you omit the auto incremented id in defaultValues it is set to a random value between 1 and 2147483647.
     *
     * The EntityManager gets determined the same way as in Entity and can be overwritten by third parameter here.
     *
     * @param string        $class         The class that should get created
     * @param array         $defaultValues The default values that came from database (for example: the created column
     *                                     has by the default the current timestamp; the id is auto incremented...)
     * @return m\Expectation
     */
    public function ormAllowInsert($class, $defaultValues = [])
    {
        /** @var EntityManager|m\MockInterface $em */
        $em = $this->ormGetEntityManagerInstance($class);

        return $em->shouldReceive('sync')->with(m::type($class))
            ->andReturnUsing(
                function (Entity $entity) use ($class, $defaultValues, $em) {
                    $expectation = $em->shouldReceive('insert')->once()
                        ->andReturnUsing(
                            function (Entity $entity, $useAutoIncrement = true) use ($class, $defaultValues, $em) {
                                if ($useAutoIncrement && !isset($defaultValues[$entity::getPrimaryKeyVars()[0]])) {
                                    $defaultValues[$entity::getPrimaryKeyVars()[0]] = mt_rand(1, pow(2, 31) - 1);
                                }
                                $entity->setOriginalData(array_merge(
                                    $this->ormAttributesToData($class, $defaultValues),
                                    $entity->getData()
                                ));
                                $entity->reset();
                                $em->map($entity);
                                return true;
                            }
                        );

                    try {
                        $entity->getPrimaryKey();
                        $expectation->with(m::type($class), false);
                        return false;
                    } catch (IncompletePrimaryKey $ex) {
                        $expectation->with(m::type($class));
                        throw $ex;
                    }
                }
            );
    }

    /**
     * Expect save on $entity
     *
     * Entity has to be a mock use `emCreateMockedEntity()` to create it.
     *
     * @param Entity|m\MockInterface $entity
     * @param array  $changingData Emulate changing data during update statement (triggers etc)
     * @param array  $updatedData  Emulate data changes in database
     */
    public function ormExpectUpdate(m\MockInterface $entity, $changingData = [], $updatedData = [])
    {
        $expectation = $this->ormAllowUpdate($entity, $changingData, $updatedData);
        $expectation->once();
    }

    /**
     * Allow save on $entity
     *
     * Entity has to be a mock use `emCreateMockedEntity()` to create it.
     *
     * @param Entity|m\MockInterface $entity
     * @param array $changingData Emulate changing data during update statement (triggers etc)
     * @param array $updatedData Emulate data changes in database
     * @return m\Expectation
     */
    public function ormAllowUpdate(m\MockInterface $entity, $changingData = [], $updatedData = [])
    {
        $expectation = $entity->shouldReceive('save');

        if ($expectation instanceof m\CompositeExpectation) {
            $expectation->andReturnUsing(
                function () use ($entity, $updatedData, $changingData) {
                    $class = get_class($entity);
                    // sync with database using $updatedData
                    if (!empty($updatedData)) {
                        $newData = $entity->getData();
                        $entity->reset();
                        $entity->setOriginalData(array_merge(
                            $entity->getData(),
                            $this->ormAttributesToData($class, $updatedData)
                        ));
                        $entity->fill($newData);
                    }

                    if (!$entity->isDirty()) {
                        return $entity;
                    }

                    // update the entity using $changingData
                    $entity->preUpdate();
                    $entity->setOriginalData(array_merge(
                        $entity->getData(),
                        $this->ormAttributesToData($class, $changingData)
                    ));
                    $entity->reset();
                    $entity->postUpdate();

                    return $entity;
                }
            );
        }

        return $expectation;
    }

    /**
     * Expect delete on $em
     *
     * If $em is not given it is determined by get_class($entity).
     *
     * If $entity is a string then it is assumed to be a class name.
     *
     * @param string|Entity $entity
     */
    public function ormExpectDelete($entity)
    {
        $expectation = $this->ormAllowDelete($entity);
        $expectation->once();
    }

    /**
     * Allow delete on $em
     *
     * If $em is not given it is determined by get_class($entity).
     *
     * If $entity is a string then it is assumed to be a class name.
     *
     * @param string|Entity $entity
     * @return m\Expectation
     */
    public function ormAllowDelete($entity)
    {
        $class = is_string($entity) ? $entity : get_class($entity);

        /** @var EntityManager|m\MockInterface $em */
        $em = $this->ormGetEntityManagerInstance($class);

        $expectation = $em->shouldReceive('delete');
        if (is_string($entity)) {
            $expectation->with(m::type($class));
        } else {
            $expectation->with($entity);
        }
        $expectation->once()->andReturnUsing(
            function (Entity $entity) {
                $entity->setOriginalData([]);
                return true;
            }
        );

        return $expectation;
    }
}
