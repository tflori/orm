<?php

namespace ORM;

use Mockery as m;
use Mockery\Mock;
use ORM\Exception\IncompletePrimaryKey;

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
     * @return Mock|EntityManager
     */
    public function emInitMock($options = [], $driver = 'mysql')
    {
        /** @var EntityManager|Mock $em */
        $em = m::mock(EntityManager::class, [$options])->makePartial();
        /** @var \PDO|Mock $pdo */
        $pdo = m::mock(\PDO::class);

        $pdo->shouldReceive('setAttribute')->andReturn(true)->byDefault();
        $pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn($driver)->byDefault();
        $pdo->shouldReceive('quote')->with(stringValue())->andReturnUsing(function ($str) {
            return '\'' . addcslashes($str, '\'') . '\'';
        })->byDefault();

        $em->setConnection($pdo);
        return $em;
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
    public function emExpectInsert($class, $defaultValues = [], $em = null)
    {
        /** @var EntityManager|Mock $em */
        $em = $em ?: EntityManager::getInstance($class);

        if (!is_callable([$em, 'shouldReceive'])) {
            throw new Exception('EntityManager mock not initialized');
        }

        $em->shouldReceive('sync')->with(m::type($class))->once()
            ->andReturnUsing(function (Entity $entity, $reset = false) {
                $entity->getPrimaryKey(); // this may throw
                return false;
            });

        $em->shouldReceive('insert')->with(m::on(function ($entity, $useAutoIncrement = true) use ($class) {
            if ($entity instanceof $class) {
                return true;
            }
            return false;
        }))->once()->andReturnUsing(function (Entity $entity, $useAutoIncrement = true) use ($defaultValues, $em) {
            if ($useAutoIncrement && !isset($defaultValues[$entity::getPrimaryKeyVars()[0]])) {
                $defaultValues[$entity::getPrimaryKeyVars()[0]] = mt_rand(1, pow(2, 31) - 1);
            }
            $entity->setOriginalData(array_merge($defaultValues, $entity->getData()));
            $entity->reset();
            $em->map($entity);
            return true;
        });
    }

    /**
     * Expect fetch for $class
     *
     * Mocks and expects an EntityFetcher with $entities as result.
     *
     * @param string        $class    The class that should be fetched
     * @param array         $entities The entities that get returned from fetcher
     * @param EntityManager $em
     * @return Mock
     * @throws Exception
     */
    public function emExpectFetch($class, $entities = [], $em = null)
    {
        /** @var EntityManager|Mock $em */
        $em = $em ?: EntityManager::getInstance($class);

        if (!is_callable([$em, 'shouldReceive'])) {
            throw new Exception('EntityManager mock not initialized');
        }

        $fetcher = \Mockery::mock(EntityFetcher::class, [$em, $class])->makePartial();
        $em->shouldReceive('fetch')->with($class)->once()->andReturn($fetcher);

        $fetcher->shouldReceive('one')->with()->andReturnValues($entities)->byDefault();
        $fetcher->shouldReceive('count')->with()->andReturn(count($entities))->byDefault();

        return $fetcher;
    }
}
