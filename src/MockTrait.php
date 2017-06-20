<?php

namespace ORM;

use Mockery as m;
use ORM\Exception\IncompletePrimaryKey;

/**
 * MockTrait for ORM
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
trait MockTrait
{

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
     * @param array  $options Options passed to EntityManager constructor
     * @param string $driver  Database driver you are using (results in different dbal instance)
     * @return m\MockInterface|EntityManager
     */
    public function ormInitMock($options = [], $driver = 'mysql')
    {
        /** @var EntityManager|m\MockInterface $em */
        $em = m::mock(EntityManager::class, [ $options ])->makePartial();
        /** @var \PDO|m\MockInterface $pdo */
        $pdo = m::mock(\PDO::class);

        $pdo->shouldReceive('setAttribute')->andReturn(true)->byDefault();
        $pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn($driver)->byDefault();
        $pdo->shouldReceive('quote')->with(stringValue())->andReturnUsing(
            function ($str) {
                return '\'' . addcslashes($str, '\'') . '\'';
            }
        )->byDefault();

        $em->setConnection($pdo);
        return $em;
    }

    /**
     * Create a partial mock of Entity $class
     *
     * @param string        $class
     * @param array         $data
     * @param EntityManager $em
     * @return m\MockInterface|Entity
     */
    public function ormCreateMockedEntity($class, $data = [], $em = null)
    {
        $em = $em ?: EntityManager::getInstance($class);

        /** @var Entity|m\MockInterface $entity */
        $entity = m::mock($class)->makePartial();
        $entity->setEntityManager($em);
        $entity->setOriginalData($data);
        $entity->reset();

        try {
            $em->map($entity, true, $class);
        } catch (IncompletePrimaryKey $ex) {
            // we tried to map but ignore primary key missing
        }
        return $entity;
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
     * @param EntityManager $em
     * @throws Exception
     */
    public function ormExpectInsert($class, $defaultValues = [], $em = null)
    {
        /** @var EntityManager|m\MockInterface $em */
        $em = $em ?: EntityManager::getInstance($class);

        $em->shouldReceive('sync')->with(m::type($class))->once()
            ->andReturnUsing(
                function (Entity $entity, $reset = false) use ($class, $defaultValues, $em) {
                    $expectation = $em->shouldReceive('insert')->once()
                        ->andReturnUsing(
                            function (Entity $entity, $useAutoIncrement = true) use ($defaultValues, $em) {
                                if ($useAutoIncrement && !isset($defaultValues[$entity::getPrimaryKeyVars()[0]])) {
                                    $defaultValues[$entity::getPrimaryKeyVars()[0]] = mt_rand(1, pow(2, 31) - 1);
                                }
                                $entity->setOriginalData(array_merge($defaultValues, $entity->getData()));
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
     * Expect fetch for $class
     *
     * Mocks and expects an EntityFetcher with $entities as result.
     *
     * @param string        $class    The class that should be fetched
     * @param array         $entities The entities that get returned from fetcher
     * @param EntityManager $em
     * @return m\MockInterface|EntityFetcher
     * @throws Exception
     */
    public function ormExpectFetch($class, $entities = [], $em = null)
    {
        /** @var EntityManager|m\MockInterface $em */
        $em = $em ?: EntityManager::getInstance($class);

        /** @var m\MockInterface|EntityFetcher $fetcher */
        $fetcher = m::mock(EntityFetcher::class, [ $em, $class ])->makePartial();
        $em->shouldReceive('fetch')->with($class)->once()->andReturn($fetcher);

        $fetcher->shouldReceive('count')->with()->andReturn(count($entities))->byDefault();
        array_push($entities, null);
        $fetcher->shouldReceive('one')->with()->andReturnValues($entities)->byDefault();

        return $fetcher;
    }

    /**
     * Expect save on $entity
     *
     * Entity has to be a mock use `emCreateMockedEntity()` to create it.
     *
     * @param Entity $entity
     * @param array  $changingData Emulate changing data during update statement (triggers etc)
     * @param array  $updatedData  Emulate data changes in database
     */
    public function ormExpectUpdate(Entity $entity, $changingData = [], $updatedData = [])
    {
        $entity->shouldReceive('save')->once()->andReturnUsing(
            function () use ($entity, $updatedData, $changingData) {
                // sync with database using $updatedData
                if (!empty($updatedData)) {
                    $newData = $entity->getData();
                    $entity->reset();
                    $entity->setOriginalData(array_merge($entity->getData(), $updatedData));
                    $entity->fill($newData);
                }

                if (!$entity->isDirty()) {
                    return $entity;
                }

                // update the entity using $changingData
                $entity->preUpdate();
                $entity->setOriginalData(array_merge($entity->getData(), $changingData));
                $entity->reset();
                $entity->postUpdate();

                return $entity;
            }
        );
    }

    /**
     * Expect delete on $em
     *
     * If $em is not given it is determined by get_class($entity).
     *
     * If $entity is a string then it is assumed to be a class name.
     *
     * @param string|Entity $entity
     * @param EntityManager $em
     */
    public function ormExpectDelete($entity, $em = null)
    {
        $class = is_string($entity) ? $entity : get_class($entity);

        /** @var EntityManager|m\MockInterface $em */
        $em = $em ?: EntityManager::getInstance($class);

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
    }
}
