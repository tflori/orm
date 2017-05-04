<?php

namespace ORM;
use ORM\Dbal\Column;
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
}
