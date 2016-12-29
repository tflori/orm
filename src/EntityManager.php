<?php

namespace ORM;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\NoConnection;
use ORM\Exceptions\NoEntity;
use ORM\Exceptions\NotScalar;
use ORM\Exceptions\UnsupportedDriver;

/**
 * The EntityManager that manages the instances of Entities.
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class EntityManager
{
    const OPT_DEFAULT_CONNECTION   = 'connection';
    const OPT_CONNECTIONS          = 'connections';
    const OPT_MYSQL_BOOLEAN_TRUE   = 'mysqlTrue';
    const OPT_MYSQL_BOOLEAN_FALSE  = 'mysqlFalse';
    const OPT_SQLITE_BOOLEAN_TRUE  = 'sqliteTrue';
    const OPT_SQLITE_BOOLEAN_FASLE = 'sqliteFalse';
    const OPT_PGSQL_BOOLEAN_TRUE   = 'pgsqlTrue';
    const OPT_PGSQL_BOOLEAN_FALSE  = 'pgsqlFalse';

    /** Named connections to database
     * @var \PDO[]|callable[]|DbConfig[] */
    protected $connections = [];

    /** The Entity map
     * @var Entity[][] */
    protected $map = [];

    /** The options set for this instance
     * @var array */
    protected $options = [
        self::OPT_MYSQL_BOOLEAN_TRUE   => '1',
        self::OPT_MYSQL_BOOLEAN_FALSE  => '0',
        self::OPT_SQLITE_BOOLEAN_TRUE  => '1',
        self::OPT_SQLITE_BOOLEAN_FASLE => '0',
        self::OPT_PGSQL_BOOLEAN_TRUE   => 'true',
        self::OPT_PGSQL_BOOLEAN_FALSE  => 'false'
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
            switch ($option) {
                case self::OPT_DEFAULT_CONNECTION:
                    $this->setConnection('default', $value);
                    break;
                case self::OPT_CONNECTIONS:
                    if (!is_array($value)) {
                        throw new InvalidConfiguration('OPT_CONNECTIONS requires an array');
                    }
                    foreach ($value as $name => $connection) {
                        $this->setConnection($name, $connection);
                    }
                    break;
            }
        }
    }

    /**
     * Add connection after instantiation
     *
     * The connection can be an array of parameters for DbConfig::__construct(), a callable function that returns a PDO
     * instance, an instance of DbConfig or a PDO instance itself.
     *
     * When it is not a PDO instance the connection get established on first use.
     *
     * @param string                       $name       Name of the connection
     * @param \PDO|callable|DbConfig|array $connection A configuration for (or a) PDO instance
     * @throws InvalidConfiguration
     */
    public function setConnection($name, $connection)
    {
        if (is_callable($connection) || $connection instanceof DbConfig || $connection instanceof \PDO) {
            $this->connections[$name] = $connection;
        } elseif (is_array($connection)) {
            $dbConfigReflection = new \ReflectionClass(DbConfig::class);
            $this->connections[$name] = $dbConfigReflection->newInstanceArgs($connection);
        } else {
            throw new InvalidConfiguration(
                'Connection must be callable, DbConfig, PDO or an array of parameters for DbConfig::__constructor'
            );
        }
    }

    /**
     * Get the pdo connection for $name.
     *
     * @param string $name Name of the connection
     * @return \PDO
     * @throws NoConnection
     */
    public function getConnection($name = 'default')
    {
        if (!isset($this->connections[$name])) {
            throw new NoConnection('Unknown database connection ' . $name);
        }

        if (!$this->connections[$name] instanceof \PDO) {
            if ($this->connections[$name] instanceof DbConfig) {
                /** @var DbConfig $dbConfig */
                $dbConfig = $this->connections[$name];
                $this->connections[$name] = $pdo = new \PDO(
                    $dbConfig->getDsn(),
                    $dbConfig->user,
                    $dbConfig->pass
                );

                foreach ($dbConfig->attributes as $attribute => $value) {
                    $pdo->setAttribute($attribute, $value);
                }
            } else {
                $pdo = call_user_func($this->connections[$name]);
                if (!$pdo instanceof \PDO) {
                    throw new NoConnection('Getter for ' . $name . ' does not return PDO instance');
                }
                $this->connections[$name] = $pdo;
            }
            $this->connections[$name]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return $this->connections[$name];
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

        $fetcher = $this->fetch(get_class($entity));
        foreach ($entity->getPrimaryKey() as $var => $value) {
            $fetcher->where($var, $value);
        }

        $result = $this->getConnection($entity::$connection)->query($fetcher->getQuery());
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
     * Insert $data in $table on $connection
     *
     * $data needs to be an array in form $col => $value and only with scalar data.
     *
     * @param Entity $entity
     * @param bool   $useAutoIncrement
     * @return mixed Returns boolean if it is not auto incremented or the $autoIncremented column
     * @throws Exceptions\InvalidName
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoConnection
     * @throws NoEntity
     * @throws UnsupportedDriver
     * @internal
     */
    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        $data = $entity->getData();

        $cols = array_keys($data);
        $values = array_values(array_map(function ($value) use ($entity) {
            return $this->convertValue($value, $entity::$connection);
        }, $data));

        $statement = 'INSERT INTO ' . $entity::getTableName()
                   . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';
        $pdo = $this->getConnection($entity::$connection);

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            switch ($driver) {
                case 'sqlite':
                    $pdo->query($statement);
                    $id = $pdo->lastInsertId();
                    break;

                case 'mysql':
                    $pdo->query($statement);
                    $id = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
                    break;

                case 'pgsql':
                    $statement .= ' RETURNING ' . $entity::getColumnName($entity::getPrimaryKeyVars()[0]);
                    $result = $pdo->query($statement);
                    $id = $result->fetchColumn();
                    break;

                default:
                    throw new UnsupportedDriver('Auto incremented column for driver ' . $driver . ' is not supported');
            }

            return $id;
        }

        $pdo->query($statement);
        $this->sync($entity, true);
        return true;
    }

    /**
     * Update $table
     *
     * Set $data where $primaryKey on $connection. $data and $primaryKey need to be an array in form $col => $value.
     *
     * @param Entity $entity
     * @return bool
     * @throws Exceptions\InvalidName
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoConnection
     * @throws NoEntity
     * @throws NotScalar
     * @internal
     */
    public function update(Entity $entity)
    {
        $data = $entity->getData();
        $primaryKey = $entity->getPrimaryKey();

        $where = [];
        foreach ($primaryKey as $var => $value) {
            $col = $entity::getColumnName($var);
            $where[] = $col . ' = ' . $this->convertValue($value, $entity::$connection);
            if (isset($data[$col])) {
                unset($data[$col]);
            }
        }

        $set = [];
        foreach ($data as $col => $value) {
            $set[] = $col . ' = ' . $this->convertValue($value, $entity::$connection);
        }

        $statement = 'UPDATE ' . $entity::getTableName()
                   . ' SET ' . implode(',', $set)
                   . ' WHERE ' . implode(' AND ', $where);
        $this->getConnection($entity::$connection)->query($statement);

        $this->sync($entity, true);
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
     * @return Entity
     * @throws IncompletePrimaryKey
     */
    public function map(Entity $entity, $update = false)
    {
        $class = get_class($entity);
        $key = md5(serialize($entity->getPrimaryKey()));

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
     * @param string|Entity $class      The entity class you want to fetch
     * @param mixed         $primaryKey The primary key of the entity you want to fetch
     * @return Entity|EntityFetcher
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoConnection
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
            $primaryKey = [$primaryKey];
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
        foreach ($primaryKey as $var => $value) {
            $fetcher->where($var, $value);
        }

        return $fetcher->one();
    }

    /**
     * Returns the given $value formatted to use in a sql statement.
     *
     * @param  mixed  $value      The variable that should be returned in SQL syntax
     * @param  string $connection The connection to use for quoting
     * @return string
     * @throws NoConnection
     * @throws NotScalar
     */
    public function convertValue($value, $connection = 'default')
    {
        switch (strtolower(gettype($value))) {
            case 'string':
                return $this->getConnection($connection)->quote($value);
                break;

            case 'integer':
                return (string)$value;
                break;

            case 'double':
                return (string)$value;
                break;

            case 'boolean':
                $connectionType = $this->getConnection($connection)->getAttribute(\PDO::ATTR_DRIVER_NAME);
                return ($value) ? $this->options[$connectionType . 'True'] : $this->options[$connectionType . 'False'];
                break;

            case 'null':
                return 'NULL';
                break;

            default:
                throw new NotScalar('$value has to be scalar data type. ' . gettype($value) . ' given');
        }
    }
}
