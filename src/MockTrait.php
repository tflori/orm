<?php /** @noinspection PhpDocMissingThrowsInspection */

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
     * @param array $options Options passed to EntityManager constructor
     * @param string $driver Database driver you are using (results in different dbal instance)
     * @return m\Mock|EntityManager
     */
    public function ormInitMock($options = [], $driver = 'mysql')
    {
        /** @var EntityManager|m\Mock $em */
        $em = m::mock(EntityManager::class)->makePartial();
        $em->__construct($options);
        /** @var \PDO|m\Mock $pdo */
        $pdo = m::mock(\PDO::class);

        $pdo->shouldReceive('setAttribute')->andReturn(true)->byDefault();
        $pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn($driver)->byDefault();
        $pdo->shouldReceive('quote')->with(m::type('string'))->andReturnUsing(
            function ($str) {
                return '\'' . addcslashes($str, '\'') . '\'';
            }
        )->byDefault();

        $em->setConnection($pdo);
        return $em;
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
     * @param string        $class
     * @param array         $data
     * @param EntityManager $em
     * @return m\Mock|Entity
     */
    public function ormCreateMockedEntity($class, $data = [], $em = null)
    {
        $em = $em ?: EntityManager::getInstance($class);

        /** @var Entity|m\Mock $entity */
        $entity = m::mock($class)->makePartial();
        $entity->setEntityManager($em);
        $entity->setOriginalData($this->ormAttributesToData($class, $data));
        $entity->reset();

        try {
            /** @scrutinizer ignore-type */
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
     */
    public function ormExpectInsert($class, $defaultValues = [], $em = null)
    {
        $expectation = $this->ormAllowInsert($class, $defaultValues, $em);
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
     * @param EntityManager $em
     * @return m\Expectation
     */
    public function ormAllowInsert($class, $defaultValues = [], $em = null)
    {
        /** @var EntityManager|m\Mock $em */
        $em = $em ?: EntityManager::getInstance($class);

        /** @scrutinizer ignore-call */
        $expectation = $em->shouldReceive('sync')->with(m::type($class))
            ->andReturnUsing(
                function (Entity $entity) use ($class, $defaultValues, $em) {
                    /** @scrutinizer ignore-call */
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

        return $expectation;
    }

    /**
     * Expect fetch for $class
     *
     * Mocks and expects an EntityFetcher with $entities as result.
     *
     * @param string        $class    The class that should be fetched
     * @param array         $entities The entities that get returned from fetcher
     * @param EntityManager $em
     * @return m\Mock|EntityFetcher
     */
    public function ormExpectFetch($class, $entities = [], $em = null)
    {
        list($expectation, $fetcher) = $this->ormAllowFetch($class, $entities, $em);
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
     * @param EntityManager $em
     * @return m\Expectation|EntityFetcher|m\Mock[]
     */
    public function ormAllowFetch($class, $entities = [], $em = null)
    {
        /** @var EntityManager|m\Mock $em */
        $em = $em ?: EntityManager::getInstance($class);

        /** @var m\Mock|EntityFetcher $fetcher */
        $fetcher = m::mock(EntityFetcher::class, [ $em, $class ])->makePartial();
        $expectation = $em->shouldReceive('fetch')->with($class)->andReturn($fetcher);

        /** @scrutinizer ignore-call */
        $fetcher->shouldReceive('count')->with()->andReturn(count($entities))->byDefault();
        array_push($entities, null);
        $fetcher->shouldReceive('one')->with()->andReturnValues($entities)->byDefault();

        return [$expectation, $fetcher];
    }

    /**
     * Expect save on $entity
     *
     * Entity has to be a mock use `emCreateMockedEntity()` to create it.
     *
     * @param Entity|m\Mock $entity
     * @param array  $changingData Emulate changing data during update statement (triggers etc)
     * @param array  $updatedData  Emulate data changes in database
     */
    public function ormExpectUpdate(Entity $entity, $changingData = [], $updatedData = [])
    {
        $expectation = $this->ormAllowUpdate($entity, $changingData, $updatedData);
        $expectation->once();
    }

    /**
     * Allow save on $entity
     *
     * Entity has to be a mock use `emCreateMockedEntity()` to create it.
     *
     * @param Entity|m\Mock $entity
     * @param array $changingData Emulate changing data during update statement (triggers etc)
     * @param array $updatedData Emulate data changes in database
     * @return m\Expectation
     */
    public function ormAllowUpdate(Entity $entity, $changingData = [], $updatedData = [])
    {
        /** @scrutinizer ignore-call */
        $expectation = $entity->shouldReceive('save')->andReturnUsing(
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
     * @param EntityManager $em
     */
    public function ormExpectDelete($entity, $em = null)
    {
        $expectation = $this->ormAllowDelete($entity, $em);
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
     * @param EntityManager $em
     * @return m\Expectation
     */
    public function ormAllowDelete($entity, $em = null)
    {
        $class = is_string($entity) ? $entity : get_class($entity);

        /** @var EntityManager|m\Mock $em */
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

        return $expectation;
    }
}
