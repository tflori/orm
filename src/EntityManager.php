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
    const OPT_CONNECTION             = 'connection';
    const OPT_MYSQL_BOOLEAN_TRUE     = 'mysqlTrue';
    const OPT_MYSQL_BOOLEAN_FALSE    = 'mysqlFalse';
    const OPT_SQLITE_BOOLEAN_TRUE    = 'sqliteTrue';
    const OPT_SQLITE_BOOLEAN_FASLE   = 'sqliteFalse';
    const OPT_PGSQL_BOOLEAN_TRUE     = 'pgsqlTrue';
    const OPT_PGSQL_BOOLEAN_FALSE    = 'pgsqlFalse';
    const OPT_QUOTING_CHARACTER      = 'quotingChar';
    const OPT_IDENTIFIER_DIVIDER     = 'identifierDivider';
    const OPT_TABLE_NAME_TEMPLATE    = 'tableNameTemplate';
    const OPT_NAMING_SCHEME_TABLE    = 'namingSchemeTable';
    const OPT_NAMING_SCHEME_COLUMN   = 'namingSchemeColumn';
    const OPT_NAMING_SCHEME_METHODS  = 'namingSchemeMethods';

    /** Connection to database
     * @var \PDO|callable|DbConfig */
    protected $connection;

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
        self::OPT_PGSQL_BOOLEAN_FALSE  => 'false',
        self::OPT_QUOTING_CHARACTER    => '"',
        self::OPT_IDENTIFIER_DIVIDER   => '.',
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
                case self::OPT_CONNECTION:
                    $this->setConnection($value);
                    break;

                case self::OPT_TABLE_NAME_TEMPLATE:
                    Entity::setTableNameTemplate($value);
                    break;

                case self::OPT_NAMING_SCHEME_TABLE:
                    Entity::setNamingSchemeTable($value);
                    break;

                case self::OPT_NAMING_SCHEME_COLUMN:
                    Entity::setNamingSchemeColumn($value);
                    break;

                case self::OPT_NAMING_SCHEME_METHODS:
                    Entity::setNamingSchemeMethods($value);
                    break;

                default:
                    $this->setOption($option, $value);
            }
        }
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
     * @param \PDO|callable|DbConfig|array $connection A configuration for (or a) PDO instance
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
     * Get the pdo connection for $name.
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
                $dbConfig = $this->connection;
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
        foreach ($entity->getPrimaryKey() as $var => $value) {
            $fetcher->where($var, $value);
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
     * @return mixed
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

        $cols = array_map(function ($key) {
            return $this->escapeIdentifier($key);
        }, array_keys($data));

        $values = array_map(function ($value) use ($entity) {
            return $this->escapeValue($value);
        }, array_values($data));

        $statement = 'INSERT INTO ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                        '(' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';
        $pdo = $this->getConnection();

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
     * Update $entity in database
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
            $where[] = $this->escapeIdentifier($col) . ' = ' . $this->escapeValue($value);
            if (isset($data[$col])) {
                unset($data[$col]);
            }
        }

        $set = [];
        foreach ($data as $col => $value) {
            $set[] = $this->escapeIdentifier($col) . ' = ' . $this->escapeValue($value);
        }

        $statement = 'UPDATE ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                        'SET ' . implode(',', $set) . ' ' .
                        'WHERE ' . implode(' AND ', $where);
        $this->getConnection()->query($statement);

        $this->sync($entity, true);
        return true;
    }

    /**
     * Delete $entity from database
     *
     * This method does not delete from the map - you can still receive the entity via fetch.
     *
     * @param Entity $entity
     * @return bool
     * @throws Exceptions\InvalidName
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoConnection
     * @throws NotScalar
     */
    public function delete(Entity $entity)
    {
        $primaryKey = $entity->getPrimaryKey();
        $where = [];
        foreach ($primaryKey as $var => $value) {
            $col = $entity::getColumnName($var);
            $where[] = $this->escapeIdentifier($col) . ' = ' . $this->escapeValue($value);
        }

        $statement = 'DELETE FROM ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                        'WHERE ' . implode(' AND ', $where);
        $this->getConnection()->query($statement);

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
     * Returns $value formatted to use in a sql statement.
     *
     * @param  mixed  $value      The variable that should be returned in SQL syntax
     * @return string
     * @throws NoConnection
     * @throws NotScalar
     */
    public function escapeValue($value)
    {
        switch (strtolower(gettype($value))) {
            case 'string':
                return $this->getConnection()->quote($value);

            case 'integer':
                return (string) $value;

            case 'double':
                return (string) $value;

            case 'boolean':
                $connectionType = $this->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);
                return ($value) ? $this->options[$connectionType . 'True'] : $this->options[$connectionType . 'False'];

            case 'null':
                return 'NULL';

            default:
                throw new NotScalar('$value has to be scalar data type. ' . gettype($value) . ' given');
        }
    }

    /**
     * Returns $identifier quoted for use in a sql statement
     *
     * @param string $identifier Identifier to quote
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        $q = $this->options[self::OPT_QUOTING_CHARACTER];
        $d = $this->options[self::OPT_IDENTIFIER_DIVIDER];
        return $q . str_replace($d, $q . $d . $q, $identifier) . $q;
    }
}
