<?php

namespace ORM;

use ORM\Dbal\Column;
use ORM\Dbal\Dbal;
use ORM\Dbal\Other;
use ORM\Dbal\Table;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\NoConnection;
use ORM\Exception\NoEntity;

/**
 * The EntityManager that manages the instances of Entities.
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class EntityManager
{
    const OPT_CONNECTION            = 'connection';
    const OPT_TABLE_NAME_TEMPLATE   = 'tableNameTemplate';
    const OPT_NAMING_SCHEME_TABLE   = 'namingSchemeTable';
    const OPT_NAMING_SCHEME_COLUMN  = 'namingSchemeColumn';
    const OPT_NAMING_SCHEME_METHODS = 'namingSchemeMethods';
    const OPT_QUOTING_CHARACTER     = 'quotingChar';
    const OPT_IDENTIFIER_DIVIDER    = 'identifierDivider';
    const OPT_BOOLEAN_TRUE          = 'true';
    const OPT_BOOLEAN_FALSE         = 'false';
    const OPT_DBAL_CLASS            = 'dbalClass';

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

    /** Connection to database
     * @var \PDO|callable|DbConfig */
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
     * @throws InvalidConfiguration
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
     * It first gets tries the EntityManager for the Namespace of $class, then for the parents of $class. If no
     * EntityManager is found it returns the last created EntityManager (null if no EntityManager got created).
     *
     * @param string $class
     * @return EntityManager
     */
    public static function getInstance($class = null)
    {
        if (empty($class)) {
            $trace = debug_backtrace();
            if (empty($trace[1]['class'])) {
                return self::$emMapping['last'];
            }
            $class = $trace[1]['class'];
        }

        if (!isset(self::$emMapping['byClass'][$class])) {
            if (!($em = self::getInstanceByParent($class)) && !($em = self::getInstanceByNameSpace($class))) {
                return self::$emMapping['last'];
            }

            self::$emMapping['byClass'][$class] = $em;
        }

        return self::$emMapping['byClass'][$class];
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

        $reflection = new \ReflectionClass($class);
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
     * @return self
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
     * @return self
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
     * @param mixed  $value
     * @return self
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
            if ($connection instanceof \PDO) {
                $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->connection = $connection;
            } elseif (is_array($connection)) {
                $dbConfigReflection = new \ReflectionClass(DbConfig::class);
                $this->connection   = $dbConfigReflection->newInstanceArgs($connection);
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
     * @return \PDO
     * @throws NoConnection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            throw new NoConnection('No database connection');
        }

        if (!$this->connection instanceof \PDO) {
            if ($this->connection instanceof DbConfig) {
                /** @var DbConfig $dbConfig */
                $dbConfig         = $this->connection;
                $this->connection = new \PDO(
                    $dbConfig->getDsn(),
                    $dbConfig->user,
                    $dbConfig->pass,
                    $dbConfig->attributes
                );
            } else {
                $pdo = call_user_func($this->connection);
                if (!$pdo instanceof \PDO) {
                    throw new NoConnection('Getter does not return PDO instance');
                }
                $this->connection = $pdo;
            }
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this->connection;
    }

    /**
     * Get the Datbase Abstraction Layer
     *
     * @return Dbal
     */
    public function getDbal()
    {
        if (!$this->dbal) {
            $connectionType = $this->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
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
     * Synchronizing $entity with database
     *
     * If $reset is true it also calls reset() on $entity.
     *
     * @param Entity $entity
     * @param bool   $reset Reset entities current data
     * @return bool
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoConnection
     * @throws NoEntity
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
        if ($originalData = $result->fetch(\PDO::FETCH_ASSOC)) {
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
     * @param bool   $useAutoIncrement
     * @return bool
     * @internal
     */
    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        return $this->getDbal()->insert($entity, $useAutoIncrement);
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
        return $this->getDbal()->update($entity);
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
        $this->getDbal()->delete($entity);
        $entity->setOriginalData([]);
        return true;
    }

    /**
     * Map $entity in the entity map
     *
     * Returns the given entity or an entity that previously got mapped. This is useful to work in every function with
     * the same object.
     *
     * ```php?start_inline=true
     * $user = $enitityManager->map(new User(['id' => 42]));
     * ```
     *
     * @param Entity $entity
     * @param bool   $update Update the entity map
     * @param string $class  Overwrite the class
     * @return Entity
     */
    public function map(Entity $entity, $update = false, $class = null)
    {
        $class = $class ?: get_class($entity);
        $key   = md5(serialize($entity->getPrimaryKey()));

        if ($update || !isset($this->map[$class][$key])) {
            $this->map[$class][$key] = $entity;
        }

        return $this->map[$class][$key];
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
     * @param string $class      The entity class you want to fetch
     * @param mixed  $primaryKey The primary key of the entity you want to fetch
     * @return Entity|EntityFetcher
     * @throws IncompletePrimaryKey
     * @throws NoEntity
     */
    public function fetch($class, $primaryKey = null)
    {
        $reflection = new \ReflectionClass($class);
        if (!$reflection->isSubclassOf(Entity::class)) {
            throw new NoEntity($class . ' is not a subclass of Entity');
        }

        if ($primaryKey === null) {
            return new EntityFetcher($this, $class);
        }

        if (!is_array($primaryKey)) {
            $primaryKey = [ $primaryKey ];
        }

        $primaryKeyVars = $class::getPrimaryKeyVars();
        if (count($primaryKeyVars) !== count($primaryKey)) {
            throw new IncompletePrimaryKey(
                'Primary key consist of [' . implode(',', $primaryKeyVars) . '] only ' . count($primaryKey) . ' given'
            );
        }

        $primaryKey = array_combine($primaryKeyVars, $primaryKey);

        if (isset($this->map[$class][md5(serialize($primaryKey))])) {
            return $this->map[$class][md5(serialize($primaryKey))];
        }

        $fetcher = new EntityFetcher($this, $class);
        foreach ($primaryKey as $attribute => $value) {
            $fetcher->where($attribute, $value);
        }

        return $fetcher->one();
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
}
