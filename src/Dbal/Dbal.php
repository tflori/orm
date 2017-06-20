<?php

namespace ORM\Dbal;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Exception\NotScalar;
use ORM\Exception\UnsupportedDriver;

/**
 * Base class for database abstraction
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Dbal
{
    /** @var array */
    protected static $typeMapping = [];

    /** @var EntityManager */
    protected $entityManager;
    /** @var string */
    protected $quotingCharacter = '"';
    /** @var string */
    protected $identifierDivider = '.';
    /** @var string */
    protected $booleanTrue = '1';
    /** @var string */
    protected $booleanFalse = '0';

    /**
     * Dbal constructor.
     *
     * @param EntityManager $entityManager
     * @param array         $options
     */
    public function __construct(EntityManager $entityManager, array $options = [])
    {
        $this->entityManager = $entityManager;

        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * Set $option to $value
     *
     * @param string $option
     * @param mixed  $value
     * @return self
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case EntityManager::OPT_IDENTIFIER_DIVIDER:
                $this->identifierDivider = $value;
                break;

            case EntityManager::OPT_QUOTING_CHARACTER:
                $this->quotingCharacter = $value;
                break;

            case EntityManager::OPT_BOOLEAN_TRUE:
                $this->booleanTrue = $value;
                break;

            case EntityManager::OPT_BOOLEAN_FALSE:
                $this->booleanFalse = $value;
                break;
        }
        return $this;
    }

    /**
     * Returns $identifier quoted for use in a sql statement
     *
     * @param string $identifier Identifier to quote
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        $q = $this->quotingCharacter;
        $d = $this->identifierDivider;
        return $q . str_replace($d, $q . $d . $q, $identifier) . $q;
    }

    /**
     * Returns $value formatted to use in a sql statement.
     *
     * @param  mixed $value The variable that should be returned in SQL syntax
     * @return string
     * @throws NotScalar
     */
    public function escapeValue($value)
    {
        $type   = is_object($value) ? get_class($value) : gettype($value);
        $method = [ $this, 'escape' . ucfirst($type) ];

        if (is_callable($method)) {
            return call_user_func($method, $value);
        } else {
            throw new NotScalar('$value has to be scalar data type. ' . gettype($value) . ' given');
        }
    }

    /**
     * Describe a table
     *
     * @param string $table
     * @return Table|Column[]
     * @throws UnsupportedDriver
     */
    public function describe($table)
    {
        throw new UnsupportedDriver('Not supported for this driver');
    }

    /**
     * Inserts $entity in database and returns success
     *
     * @param Entity $entity
     * @param bool   $useAutoIncrement
     * @return bool
     * @throws UnsupportedDriver
     */
    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            throw new UnsupportedDriver('Auto incremented column for this driver is not supported');
        }

        $this->entityManager->getConnection()->query($statement);
        return $this->entityManager->sync($entity, true);
    }

    /**
     * Update $entity in database and returns success
     *
     * @param Entity $entity
     * @return bool
     * @internal
     */
    public function update(Entity $entity)
    {
        $data       = $entity->getData();
        $primaryKey = $entity->getPrimaryKey();

        $where = [];
        foreach ($primaryKey as $attribute => $value) {
            $col     = $entity::getColumnName($attribute);
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
        $this->entityManager->getConnection()->query($statement);

        return $this->entityManager->sync($entity, true);
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
        $primaryKey = $entity->getPrimaryKey();
        $where      = [];
        foreach ($primaryKey as $attribute => $value) {
            $col     = $entity::getColumnName($attribute);
            $where[] = $this->escapeIdentifier($col) . ' = ' . $this->escapeValue($value);
        }

        $statement = 'DELETE FROM ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                     'WHERE ' . implode(' AND ', $where);
        $this->entityManager->getConnection()->query($statement);

        return true;
    }

    /**
     * Build the insert statement for $entity
     *
     * @param Entity $entity
     * @return string
     */
    protected function buildInsertStatement($entity)
    {
        $data = $entity->getData();

        $cols = array_map(
            function ($key) {
                return $this->escapeIdentifier($key);
            },
            array_keys($data)
        );

        $values = array_map(
            function ($value) use ($entity) {
                return $this->escapeValue($value);
            },
            array_values($data)
        );

        $statement = 'INSERT INTO ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                     '(' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';

        return $statement;
    }

    /**
     * Update the autoincrement value
     *
     * @param Entity     $entity
     * @param int|string $value
     */
    protected function updateAutoincrement(Entity $entity, $value)
    {
        $var    = $entity::getPrimaryKeyVars()[0];
        $column = $entity::getColumnName($var);

        $entity->setOriginalData(array_merge($entity->getData(), [ $column => $value ]));
        $entity->__set($var, $value);
    }

    /**
     * Normalize $type
     *
     * The type returned by mysql is for example VARCHAR(20) - this function converts it to varchar
     *
     * @param string $type
     * @return string
     */
    protected function normalizeType($type)
    {
        $type = strtolower($type);

        if (($p = strpos($type, '(')) !== false && $p > 0) {
            $type = substr($type, 0, $p);
        }

        return trim($type);
    }

    /**
     * Extract content from parenthesis in $type
     *
     * @param string $type
     * @return string
     */
    protected function extractParenthesis($type)
    {
        if (preg_match('/\((.+)\)/', $type, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Escape a string for query
     *
     * @param string $value
     * @return string
     */
    protected function escapeString($value)
    {
        return $this->entityManager->getConnection()->quote($value);
    }

    /**
     * Escape an integer for query
     *
     * @param int $value
     * @return string
     */
    protected function escapeInteger($value)
    {
        return (string) $value;
    }

    /**
     * Escape a double for Query
     *
     * @param double $value
     * @return string
     */
    protected function escapeDouble($value)
    {
        return (string) $value;
    }

    /**
     * Escape NULL for query
     *
     * @return string
     */
    protected function escapeNULL()
    {
        return 'NULL';
    }

    /**
     * Escape a boolean for query
     *
     * @param bool $value
     * @return string
     */
    protected function escapeBoolean($value)
    {
        return ($value) ? $this->booleanTrue : $this->booleanFalse;
    }

    /**
     * Escape a date time object for query
     *
     * @param \DateTime $value
     * @return mixed
     */
    protected function escapeDateTime(\DateTime $value)
    {
        $value->setTimezone(new \DateTimeZone('UTC'));
        return $value->format('Y-m-d\TH:i:s.u\Z');
    }
}
