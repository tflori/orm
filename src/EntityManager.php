<?php

namespace ORM;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\NoConnection;
use ORM\Exceptions\NoEntity;
use ORM\Exceptions\NotScalar;

/**
 * The EntityManager that manages the instances of Entities.
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class EntityManager
{
    const OPT_DEFAULT_CONNECTION = 'connection';
    const OPT_CONNECTIONS        = 'connections';
    const OPT_MYSQL_BOOLEAN_TRUE = 'mysqlTrue';
    const OPT_MYSQL_BOOLEAN_FALSE = 'mysqlFalse';
    const OPT_SQLITE_BOOLEAN_TRUE = 'sqliteTrue';
    const OPT_SQLITE_BOOLEAN_FASLE = 'sqliteFalse';
    const OPT_PGSQL_BOOLEAN_TRUE = 'pgsqlTrue';
    const OPT_PGSQL_BOOLEAN_FALSE = 'pgsqlFalse';

    /**@var \PDO[]|callable[]|DbConfig[] */
    protected $connections = [];

    /** @var Entity[][] */
    protected $map = [];

    protected $options = [
        self::OPT_MYSQL_BOOLEAN_TRUE => '1',
        self::OPT_MYSQL_BOOLEAN_FALSE => '0',
        self::OPT_SQLITE_BOOLEAN_TRUE => '1',
        self::OPT_SQLITE_BOOLEAN_FASLE => '0',
        self::OPT_PGSQL_BOOLEAN_TRUE => 'true',
        self::OPT_PGSQL_BOOLEAN_FALSE => 'false'
    ];

    /**
     * @param array $options
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
     * Set the connection $name to $connection.
     *
     * @param string $name
     * @param \PDO|callable|DbConfig $connection
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
     * @param string $name
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
     * Save $entity with $data.
     *
     * Should not be called directly. Instead you should use $entity->save($entityManager);
     *
     * @param Entity $entity
     * @param array $data
     * @internal
     */
    public function save(Entity $entity, array $data)
    {
    }

    /**
     * @param Entity $entity
     * @return Entity
     * @throws IncompletePrimaryKey
     */
    public function map(Entity $entity)
    {
        $key = [];
        foreach ($entity::getPrimaryKey() as $var) {
            $value = $entity->$var;
            if ($value === null) {
                throw new IncompletePrimaryKey('Entity can not be mapped: ' . $var . ' is null');
            }
            $key[] = $entity->$var;
        }

        $class = get_class($entity);
        $key = md5(serialize($key));

        if (!isset($this->map[$class][$key])) {
            $this->map[$class][$key] = $entity;
        }

        return $this->map[$class][$key];
    }

    /**
     * @param string|Entity $class
     * @param mixed $primaryKey
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
            $primaryKey = [$primaryKey];
        }

        $primaryKeyVars = $class::getPrimaryKey();
        if (count($primaryKeyVars) !== count($primaryKey)) {
            throw new IncompletePrimaryKey(
                'Primary key consist of [' . implode(',', $primaryKeyVars) . '] only ' . count($primaryKey) . ' given'
            );
        }

        if (isset($this->map[$class][md5(serialize($primaryKey))])) {
            return $this->map[$class][md5(serialize($primaryKey))];
        }

        $fetcher = new EntityFetcher($this, $class);
        foreach ($primaryKeyVars as $i => $col) {
            $fetcher->where($col, $primaryKey[$i]);
        }

        return $fetcher->one();
    }

    /**
     * Returns the given $value formatted to use in a sql statement.
     *
     * @param  mixed $value The variable that should be returned in SQL syntax
     * @param  string $connection The connection to use for quoting
     * @return string
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
