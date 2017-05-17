<?php

namespace ORM;

use ORM\Dbal\Column;
use ORM\Dbal\Type;
use ORM\Dbal\TypeInterface;
use ORM\Exceptions\NotScalar;
use ORM\Exceptions\UnsupportedDriver;

/**
 * Class Dbal
 *
 * This is the base class for the database abstraction layer.
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Dbal
{
    /** @var EntityManager */
    protected $em;

    protected static $quotingCharacter = '"';
    protected static $identifierDivider = '.';
    protected static $booleanTrue = '1';
    protected static $booleanFalse = '0';

    protected static $registeredTypes = [];
    protected static $typeMapping = [];

    /**
     * Dbal constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
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
     * @param  mixed  $value      The variable that should be returned in SQL syntax
     * @return string
     * @throws NotScalar
     */
    public function escapeValue($value)
    {
        switch (strtolower(gettype($value))) {
            case 'string':
                return $this->em->getConnection()->quote($value);

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
     * @param $table
     * @return Column[]
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
     * @return bool|int
     * @throws UnsupportedDriver
     */
    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            throw new UnsupportedDriver('Auto incremented column for this driver is not supported');
        }

        $this->em->getConnection()->query($statement);
        $this->em->sync($entity, true);
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
        $this->em->getConnection()->query($statement);

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
        $this->em->getConnection()->query($statement);

        return true;
    }

    public static function registerType($type)
    {
        if (!in_array($type, static::$registeredTypes)) {
            array_unshift(static::$registeredTypes, $type);
        }
    }

    public static function setQuotingCharacter($char)
    {
        static::$quotingCharacter = $char;
    }

    public static function setIdentifierDivider($divider)
    {
        static::$identifierDivider = $divider;
    }

    public static function setBooleanTrue($true)
    {
        static::$booleanTrue = $true;
    }

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

    protected function normalizeType($type)
    {
        $type = strtolower($type);

        if (($p = strpos($type, '(')) !== false && $p > 0) {
            $type = substr($type, 0, $p);
        }

        return $type;
    }

    protected function extractParenthesis($type)
    {
        if (preg_match('/\(([\d,]+)\)/', $type, $match)) {
            return $match[1];
        }

        return null;
    }

    protected function getType($columnDefinition)
    {
        if (isset(static::$typeMapping[$columnDefinition['data_type']])) {
            return call_user_func([static::$typeMapping[$columnDefinition['data_type']], 'factory'], $columnDefinition);
        } else {
            foreach (self::$registeredTypes as $class) {
                if ($type = $class::fromDefinition($columnDefinition)) {
                    return $type;
                }
            }

            return new Type\Text();
        }
    }
}
