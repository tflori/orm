<?php

namespace ORM\Dbal;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Exceptions\NotScalar;
use ORM\Exceptions\UnsupportedDriver;

/**
 * Base class for database abstraction
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Dbal
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected static $typeMapping = [];
    /** @var string */
    protected static $quotingCharacter = '"';
    /** @var string */
    protected static $identifierDivider = '.';
    /** @var string */
    protected static $booleanTrue = '1';
    /** @var string */
    protected static $booleanFalse = '0';

    /**
     * Dbal constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns $identifier quoted for use in a sql statement
     *
     * @param string $identifier Identifier to quote
     * @return string
     */
    public function escapeIdentifier($identifier)
    {
        $q = static::$quotingCharacter;
        $d = static::$identifierDivider;
        return $q . str_replace($d, $q . $d . $q, $identifier) . $q;
    }

    /**
     * Returns $value formatted to use in a sql statement.
     *
     * @param  mixed  $value The variable that should be returned in SQL syntax
     * @return string
     * @throws NotScalar
     */
    public function escapeValue($value)
    {
        switch (strtolower(gettype($value))) {
            case 'string':
                return $this->entityManager->getConnection()->quote($value);

            case 'integer':
                return (string) $value;

            case 'double':
                return (string) $value;

            case 'null':
                return 'NULL';

            case 'boolean':
                return ($value) ? static::$booleanTrue : static::$booleanFalse;

            default:
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
     * Inserts $entity and returns the new ID for autoincrement or true
     *
     * @param Entity $entity
     * @param bool   $useAutoIncrement
     * @return mixed
     * @throws UnsupportedDriver
     */
    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            throw new UnsupportedDriver('Auto incremented column for this driver is not supported');
        }

        $this->entityManager->getConnection()->query($statement);
        $this->entityManager->sync($entity, true);
        return true;
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
        $this->entityManager->getConnection()->query($statement);

        return true;
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
        $where = [];
        foreach ($primaryKey as $var => $value) {
            $col = $entity::getColumnName($var);
            $where[] = $this->escapeIdentifier($col) . ' = ' . $this->escapeValue($value);
        }

        $statement = 'DELETE FROM ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                     'WHERE ' . implode(' AND ', $where);
        $this->entityManager->getConnection()->query($statement);

        return true;
    }

    /**
     * @param string $char
     */
    public static function setQuotingCharacter($char)
    {
        static::$quotingCharacter = $char;
    }

    /**
     * @param string $divider
     */
    public static function setIdentifierDivider($divider)
    {
        static::$identifierDivider = $divider;
    }

    /**
     * @param string $true
     */
    public static function setBooleanTrue($true)
    {
        static::$booleanTrue = $true;
    }

    /**
     * @param string $false
     */
    public static function setBooleanFalse($false)
    {
        static::$booleanFalse = $false;
    }

    /**
     * @return string
     */
    public static function getQuotingCharacter()
    {
        return static::$quotingCharacter;
    }

    /**
     * @return string
     */
    public static function getIdentifierDivider()
    {
        return static::$identifierDivider;
    }

    /**
     * @return string
     */
    public static function getBooleanTrue()
    {
        return static::$booleanTrue;
    }

    /**
     * @return string
     */
    public static function getBooleanFalse()
    {
        return static::$booleanFalse;
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

        $cols = array_map(function ($key) {
            return $this->escapeIdentifier($key);
        }, array_keys($data));

        $values = array_map(function ($value) use ($entity) {
            return $this->escapeValue($value);
        }, array_values($data));

        $statement = 'INSERT INTO ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                     '(' . implode(',', $cols) . ') VALUES (' . implode(',', $values) . ')';

        return $statement;
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

        return $type;
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
}
