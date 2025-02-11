<?php

namespace ORM;

use ORM\Dbal\Column;
use ORM\Dbal\Dbal;
use ORM\Dbal\Expression;
use ORM\Dbal\Other;
use ORM\Dbal\Table;
use ORM\Event\Deleted;
use ORM\Event\Deleting;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidArgument;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\NoConnection;
use ORM\Exception\NoEntity;
use ORM\Observer\CallbackObserver;
use ORM\QueryBuilder\QueryBuilder;
use PDO;
use ReflectionClass;

/**
 * The EntityManager that manages the instances of Entities.
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class EntityManager
{
    const OPT_CONNECTION = 'connection';
    const OPT_TABLE_NAME_TEMPLATE = 'tableNameTemplate';
    const OPT_NAMING_SCHEME_TABLE = 'namingSchemeTable';
    const OPT_NAMING_SCHEME_COLUMN = 'namingSchemeColumn';
    const OPT_NAMING_SCHEME_METHODS = 'namingSchemeMethods';
    const OPT_NAMING_SCHEME_ATTRIBUTE = 'namingSchemeAttribute';
    const OPT_QUOTING_CHARACTER = 'quotingChar';
    const OPT_IDENTIFIER_DIVIDER = 'identifierDivider';
    const OPT_BOOLEAN_TRUE = 'true';
    const OPT_BOOLEAN_FALSE = 'false';
    const OPT_DBAL_CLASS = 'dbalClass';

    /** @deprecated */
    const OPT_MYSQL_BOOLEAN_TRUE = 'mysqlTrue';
    /** @deprecated */
    const OPT_MYSQL_BOOLEAN_FALSE = 'mysqlFalse';
    /** @deprecated */
    const OPT_SQLITE_BOOLEAN_TRUE = 'sqliteTrue';
    /** @deprecated */
    const OPT_SQLITE_BOOLEAN_FALSE = 'sqliteFalse';
    /** @deprecated */
    const OPT_PGSQL_BOOLEAN_TRUE = 'pgsqlTrue';
    /** @deprecated */
    const OPT_PGSQL_BOOLEAN_FALSE = 'pgsqlFalse';

    /** @var callable */
    protected static $resolver;

    /** Connection to database
     * @var PDO|callable|DbConfig */
    protected $connection;

    /** The Database Abstraction Layer
     * @var Dbal */
    protected $dbal;

    /** The Namer instance
     * @var Namer */
    protected $namer;

    /** The Entity map
     * @var Entity[][] */
    protected $map = [];

    /** The options set for this instance
     * @var array */
    protected $options = [];

    /** Already fetched column descriptions
     * @var Table[]|Column[][] */
    protected $descriptions = [];

    /** Classes forcing bulk insert
     * @var BulkInsert[] */
    protected $bulkInserts = [];

    /** @var ObserverInterface[][] */
    protected $observers = [];

    /** Mapping for EntityManager instances
     * @var EntityManager[string]|EntityManager[string][string] */
    protected static $emMapping = [
        'byClass'     => [],
        'byNameSpace' => [],
        'byParent'    => [],
        'last'        => null,
    ];

    /**
     * Constructor
     *
     * @param array $options Options for the new EntityManager
     */
    public function __construct($options = [])
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        self::$emMapping['last'] = $this;
    }

    /**
     * Get an instance of the EntityManager.
     *
     * If no class is given it gets $class from backtrace.
     *
     * It first tries to get the EntityManager for the Namespace of $class, then for the parents of $class. If no
     * EntityManager is found it returns the last created EntityManager (null if no EntityManager got created).
     *
     * @param string $class
     * @return EntityManager
     */
    public static function getInstance($class = null)
    {
        if (self::$resolver) {
            return call_user_func(self::$resolver, $class);
        }

        if (!self::$emMapping['last']) {
            throw new Exception('No entity manager initialized');
        }

        if (empty($class)) {
            $trace = debug_backtrace();
            if (empty($trace[1]['class'])) {
                return self::$emMapping['last'];
            }
            $class = $trace[1]['class'];
        }

        if (!isset(self::$emMapping['byClass'][$class])) {
            self::$emMapping['byClass'][$class] = self::getInstanceByParent($class) ?:
                self::getInstanceByNameSpace($class) ?:
                self::$emMapping['last'];
        }

        return self::$emMapping['byClass'][$class];
    }

    /**
     * Overwrite the functionality of ::getInstance($class) by $resolver($class)
     *
     * @param callable $resolver
     */
    public static function setResolver(callable $resolver)
    {
        self::$resolver = $resolver;
    }

    /**
     * Get the instance by NameSpace mapping
     *
     * @param $class
     * @return EntityManager
     */
    private static function getInstanceByNameSpace($class)
    {
        foreach (self::$emMapping['byNameSpace'] as $nameSpace => $em) {
            if (strpos($class, $nameSpace) === 0) {
                return $em;
            }
        }

        return null;
    }

    /**
     * Get the instance by Parent class mapping
     *
     * @param $class
     * @return EntityManager
     */
    private static function getInstanceByParent($class)
    {
        // we don't need a reflection when we don't have mapping byParent
        if (empty(self::$emMapping['byParent'])) {
            return null;
        }

        $reflection = new ReflectionClass($class);
        foreach (self::$emMapping['byParent'] as $parentClass => $em) {
            if ($reflection->isSubclassOf($parentClass)) {
                return $em;
            }
        }

        return null;
    }

    /**
     * Define $this EntityManager as the default EntityManager for $nameSpace
     *
     * @param $nameSpace
     * @return $this
     */
    public function defineForNamespace($nameSpace)
    {
        self::$emMapping['byNameSpace'][$nameSpace] = $this;
        return $this;
    }

    /**
     * Define $this EntityManager as the default EntityManager for subClasses of $class
     *
     * @param $class
     * @return $this
     */
    public function defineForParent($class)
    {
        self::$emMapping['byParent'][$class] = $this;
        return $this;
    }

    /**
     * Set $option to $value
     *
     * @param string $option One of OPT_* constants
     * @param mixed $value
     * @return $this
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case self::OPT_CONNECTION:
                $this->setConnection($value);
                break;

            case self::OPT_SQLITE_BOOLEAN_TRUE:
            case self::OPT_MYSQL_BOOLEAN_TRUE:
            case self::OPT_PGSQL_BOOLEAN_TRUE:
                $option = self::OPT_BOOLEAN_TRUE;
                break;

            case self::OPT_SQLITE_BOOLEAN_FALSE:
            case self::OPT_MYSQL_BOOLEAN_FALSE:
            case self::OPT_PGSQL_BOOLEAN_FALSE:
                $option = self::OPT_BOOLEAN_FALSE;
                break;
        }

        $this->options[$option] = $value;
        return $this;
    }

    /**
     * Get $option
     *
     * @param $option
     * @return mixed
     */
    public function getOption($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * Add connection after instantiation
     *
     * The connection can be an array of parameters for DbConfig::__construct(), a callable function that returns a PDO
     * instance, an instance of DbConfig or a PDO instance itself.
     *
     * When it is not a PDO instance the connection get established on first use.
     *
     * @param mixed $connection A configuration for (or a) PDO instance
     * @throws InvalidConfiguration
     */
    public function setConnection($connection)
    {
        if (is_callable($connection) || $connection instanceof DbConfig) {
            $this->connection = $connection;
        } else {
            if ($connection instanceof PDO) {
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection = $connection;
            } elseif (is_array($connection)) {
                $this->connection = new DbConfig(...$connection);
            } else {
                throw new InvalidConfiguration(
                    'Connection must be callable, DbConfig, PDO or an array of parameters for DbConfig::__constructor'
                );
            }
        }
    }

    /**
     * Get the pdo connection.
     *
     * @return PDO
     * @throws NoConnection
     * @throws NoConnection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            throw new NoConnection('No database connection');
        }

        if (!$this->connection instanceof PDO) {
            if ($this->connection instanceof DbConfig) {
                /** @var DbConfig $dbConfig */
                $dbConfig         = $this->connection;
                $this->connection = new PDO(
                    $dbConfig->getDsn(),
                    $dbConfig->user,
                    $dbConfig->pass,
                    $dbConfig->attributes
                );
            } else {
                $pdo = call_user_func($this->connection);
                if (!$pdo instanceof PDO) {
                    throw new NoConnection('Getter does not return PDO instance');
                }
                $this->connection = $pdo;
            }
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $this->connection;
    }

    /**
     * Get the Database Abstraction Layer
     *
     * @return Dbal
     */
    public function getDbal()
    {
        if (!$this->dbal) {
            $connectionType = $this->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME);
            $options        = &$this->options;
            $dbalClass      = isset($options[self::OPT_DBAL_CLASS]) ?
                $options[self::OPT_DBAL_CLASS] : __NAMESPACE__ . '\\Dbal\\' . ucfirst($connectionType);

            if (!class_exists($dbalClass)) {
                $dbalClass = Other::class;
            }

            $this->dbal = new $dbalClass($this, $options);
        }

        return $this->dbal;
    }

    /**
     * Get the Namer instance
     *
     * @return Namer
     * @codeCoverageIgnore trivial code...
     */
    public function getNamer()
    {
        if (!$this->namer) {
            $this->namer = new Namer($this->options);
        }

        return $this->namer;
    }

    /**
     * Begin a transaction or create a savepoint
     *
     * @return bool
     * @codeCoverageIgnore trivial code
     */
    public function beginTransaction()
    {
        return $this->getDbal()->beginTransaction();
    }

    /**
     * Commit the current transaction or decrease the savepoint counter
     *
     * Actually nothing will be committed if there are savepoints. Instead the counter will be decreased and
     * the commited savepoint will still be rolled back when you call rollback afterwards.
     *
     * Hopefully that gives a hint why save points are no transactions and what the limitations are.
     * ```
     * Begin transaction
     *   updates / inserts for transaction1
     *   Create savepoint transaction1
     *     updates / inserts for transaction2
     *     Create savepoint transaction2
     *       updates / inserts for transaction3
     *     <no commit here but you called commit for transaction3>
     *     updates / inserts for transaction2
     *   rollback of transaction2 to savepoint of transaction1
     *   update / inserts for transaction1
     * commit of transaction1
     * ```
     *
     * @param bool $all Commit all opened transactions and savepoints
     * @return bool
     * @codeCoverageIgnore trivial code
     */
    public function commit($all = false)
    {
        return $this->getDbal()->commit($all);
    }

    /**
     * Rollback the current transaction or save point
     *
     * @return bool
     * @codeCoverageIgnore trivial code
     */
    public function rollback()
    {
        return $this->getDbal()->rollback();
    }

    /**
     * Get a query builder for $table
     *
     * @param string $table
     * @param string $alias
     * @return QueryBuilder
     */
    public function query($table, $alias = '')
    {
        return new QueryBuilder($table, $alias, $this);
    }

    /**
     * Create a raw expression from $expression to disable escaping
     *
     * @param string $expression
     * @return Expression
     */
    public static function raw($expression)
    {
        return new Expression($expression);
    }

    /**
     * Synchronizing $entity with database
     *
     * If $reset is true it also calls reset() on $entity.
     *
     * @param Entity $entity
     * @param bool $reset Reset entities current data
     * @return bool
     */
    public function sync(Entity $entity, $reset = false)
    {
        $this->map($entity, true);

        /** @var EntityFetcher $fetcher */
        $fetcher = $this->fetch(get_class($entity));
        foreach ($entity->getPrimaryKey() as $attribute => $value) {
            $fetcher->where($attribute, $value);
        }

        $result = $this->getConnection()->query($fetcher->getQuery());
        if ($originalData = $result->fetch(PDO::FETCH_ASSOC)) {
            $entity->setOriginalData($originalData);
            if ($reset) {
                $entity->reset();
            }
            return true;
        }
        return false;
    }

    /**
     * Insert $entity in database
     *
     * Returns boolean if it is not auto incremented or the value of auto incremented column otherwise.
     *
     * @param Entity $entity
     * @param bool $useAutoIncrement
     * @return bool
     * @internal
     */
    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        if (isset($this->bulkInserts[get_class($entity)])) {
            $this->bulkInserts[get_class($entity)]->add($entity);
            return true;
        }

        return $useAutoIncrement && $entity::isAutoIncremented() ?
            $this->getDbal()->insertAndSyncWithAutoInc($entity) :
            $this->getDbal()->insertAndSync($entity);
    }

    /**
     * Force $class to use bulk insert.
     *
     * At the end you should call finish bulk insert otherwise you may loose data.
     *
     * @param string $class
     * @param int $limit Maximum number of rows per insert
     * @return BulkInsert
     */
    public function useBulkInserts($class, $limit = 20)
    {
        if (!isset($this->bulkInserts[$class])) {
            $this->bulkInserts[$class] = new BulkInsert($this->getDbal(), $class, $limit);
        }
        return $this->bulkInserts[$class];
    }

    /**
     * Finish the bulk insert for $class.
     *
     * Returns an array of entities added.
     *
     * @param $class
     * @return Entity[]
     */
    public function finishBulkInserts($class)
    {
        $bulkInsert = $this->bulkInserts[$class];
        unset($this->bulkInserts[$class]);
        return $bulkInsert->finish();
    }

    /**
     * Update $entity in database
     *
     * @param Entity $entity
     * @return bool
     * @internal
     */
    public function update(Entity $entity)
    {
        return $this->getDbal()->updateEntity($entity);
    }

    /**
     * Delete $entity from database
     *
     * This method does not delete from the map - you can still receive the entity via fetch.
     *
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity)
    {
        if ($this->fire(new Deleting($entity)) === false) {
            return false;
        }
        $this->getDbal()->deleteEntity($entity);
        $entity->setOriginalData([], false);
        $this->fire(new Deleted($entity));
        return true;
    }

    /**
     * Map $entity in the entity map
     *
     * Returns the given entity or an entity that previously got mapped. This is useful to work in every function with
     * the same object.
     *
     * ```php
     * $user = $enitityManager->map(new User(['id' => 42]));
     * ```
     *
     * @param Entity $entity
     * @param bool $update Update the entity map
     * @param string $class Overwrite the class
     * @return Entity
     */
    public function map(Entity $entity, $update = false, $class = null)
    {
        $class = $class ?: get_class($entity);
        $key   = static::buildChecksum($entity->getPrimaryKey());

        if ($update || !isset($this->map[$class][$key])) {
            $this->map[$class][$key] = $entity;
        }

        return $this->map[$class][$key];
    }

    /**
     * Check if the entity map has $entity
     *
     * If you want to know if the entity already exists in the map use this method.
     *
     * @param Entity|string $entity
     * @param mixed $primaryKey
     * @return bool
     */
    public function has($entity, $primaryKey = null)
    {
        if ($entity instanceof Entity) {
            $class = get_class($entity);
            $key   = static::buildChecksum($entity->getPrimaryKey());
        } else {
            $class = $entity;
            $key = static::buildChecksum(static::buildPrimaryKey($class, (array)$primaryKey));
        }

        return isset($this->map[$class][$key]);
    }

    /**
     * Fetch one or more entities
     *
     * With $primaryKey it tries to find this primary key in the entity map (carefully: mostly the database returns a
     * string and we do not convert them). If there is no entity in the entity map it tries to fetch the entity from
     * the database. The return value is then null (not found) or the entity.
     *
     * Without $primaryKey it creates an entityFetcher and returns this.
     *
     * @param string $class The entity class you want to fetch
     * @param mixed $primaryKey The primary key of the entity you want to fetch
     * @return Entity|EntityFetcher
     * @throws IncompletePrimaryKey
     * @throws NoEntity
     */
    public function fetch($class, $primaryKey = null)
    {
        $reflection = new ReflectionClass($class);
        if (!$reflection->isSubclassOf(Entity::class)) {
            throw new NoEntity($class . ' is not a subclass of Entity');
        }
        /** @var string|Entity $class */

        if ($primaryKey === null) {
            return new EntityFetcher($this, $class);
        }

        $primaryKey = $this::buildPrimaryKey($class, (array)$primaryKey);
        $checksum = $this::buildChecksum($primaryKey);

        if (isset($this->map[$class][$checksum])) {
            return $this->map[$class][$checksum];
        }

        $fetcher = new EntityFetcher($this, $class);
        foreach ($primaryKey as $attribute => $value) {
            $fetcher->where($attribute, $value);
        }

        return $fetcher->one();
    }

    /**
     * Load the $relation on all $entities with the least amount of queries
     *
     * $relation can be nested by dividing them with a dot.
     *
     * @param string $relation
     * @param Entity ...$entities
     * @throws Exception\UndefinedRelation
     */
    public function eagerLoad($relation, Entity ...$entities)
    {
        $relations = explode('.', $relation); // split the relations by .
        while (count($relations) > 0 && count($entities) > 0) {
            $relation = array_shift($relations);
            $loaded = array_reduce($entities, function ($loaded, Entity $entity) use ($relation) {
                return $loaded && $entity->hasLoaded($relation);
            }, true);

            if (!$loaded) {
                // we assume that every object has the same class and is an entity
                /** @var Entity $first */
                $first = Helper::first($entities);
                $first::getRelation($relation)
                    ->eagerLoad($this, ...$entities);
            }

            if (count($relations) > 0) {
                // get all related objects of this relation
                $entities = array_merge(...array_map(function ($relatedObject) use ($relation) {
                    $related = $relatedObject->getRelated($relation);
                    return !is_array($related) ? [$related] : $related;
                }, $entities));
            }
        }
    }

    /**
     * Observe $class using $observer
     *
     * If AbstractObserver is omitted it returns a new CallbackObserver. Usage example:
     * ```php
     * $em->observe(User::class)
     *     ->on('inserted', function (User $user) { ... })
     *     ->on('deleted', function (User $user) { ... });
     * ```
     *
     * For more information about model events please consult the [documentation](https://tflori.github.io/
     *
     * @param string $class
     * @param ObserverInterface|null $observer
     * @return CallbackObserver|null
     * @throws InvalidArgument
     */
    public function observe($class, ObserverInterface $observer = null)
    {
        $returnObserver = !$observer;
        $observer || $observer = new CallbackObserver();

        if (!isset($this->observers[$class])) {
            $this->observers[$class] = [];
        } elseif (in_array($observer, $this->observers[$class], true)) {
            throw new InvalidArgument('$observer is already registered to ' . $class);
        }

        $this->observers[$class][] = $observer;
        return $returnObserver ? $observer : null;
    }

    /**
     * Detach $observer from all classes
     *
     * If the observer is attached to multiple classes all are removed except the optional parameter
     * $from defines from which class to remove the $observer.
     *
     * Returns whether or not an observer got detached.
     *
     * @param ObserverInterface $observer
     * @param string|null $from
     * @return bool
     */
    public function detach(ObserverInterface $observer, $from = null)
    {
        $removed = false;
        foreach ($this->observers as $class => &$observers) {
            if ($from !== null && $class !== $from) {
                continue;
            }

            $this->observers[$class] = array_filter(
                $observers,
                function (ObserverInterface $current) use ($observer, &$removed) {
                    return $current === $observer ? !($removed = true) : true;
                }
            );
        }

        return $removed;
    }

    /**
     * Fire $event on $entity
     *
     * @param Event $event
     * @return bool
     */
    public function fire(Event $event)
    {
        do {
            $current = isset($current) ? $current->getParentClass() : new ReflectionClass($event->entity);
            $class = $current->getName();
            /** @var ObserverInterface[] $observers */
            $observers = isset($this->observers[$class]) ? $this->observers[$class] : [];
            foreach ($observers as $observer) {
                if ($observer->handle($event) === false || $event->stopped) {
                    return $event->stopped;
                }
            }
        } while ($class !== Entity::class);

        return true;
    }

    /**
     * Returns $value formatted to use in a sql statement.
     *
     * @param  mixed $value The variable that should be returned in SQL syntax
     * @return string
     * @codeCoverageIgnore This is just a proxy
     */
    public function escapeValue($value)
    {
        return $this->getDbal()->escapeValue($value);
    }

    /**
     * Returns $identifier quoted for use in a sql statement
     *
     * @param string $identifier Identifier to quote
     * @return string
     * @codeCoverageIgnore This is just a proxy
     */
    public function escapeIdentifier($identifier)
    {
        return $this->getDbal()->escapeIdentifier($identifier);
    }

    /**
     * Returns an array of columns from $table.
     *
     * @param string $table
     * @return Column[]|Table
     */
    public function describe($table)
    {
        if (!isset($this->descriptions[$table])) {
            $this->descriptions[$table] = $this->getDbal()->describe($table);
        }
        return $this->descriptions[$table];
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

    /**
     * Builds the primary key with column names as keys
     *
     * @param string|Entity $class
     * @param array $primaryKey
     * @return array
     * @throws IncompletePrimaryKey
     */
    protected static function buildPrimaryKey($class, array $primaryKey)
    {
        $primaryKeyVars = $class::getPrimaryKeyVars();
        if (count($primaryKeyVars) !== count($primaryKey)) {
            throw new IncompletePrimaryKey(
                'Primary key consists of [' . implode(',', $primaryKeyVars) . '] only ' . count($primaryKey) . ' given'
            );
        }

        return array_combine($primaryKeyVars, $primaryKey);
    }
}
