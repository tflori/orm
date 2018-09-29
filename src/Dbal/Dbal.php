<?php

namespace ORM\Dbal;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Exception;
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
    use Escaping;

    /** @var array */
    protected static $typeMapping = [];

    /** @var EntityManager */
    protected $entityManager;

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
     * @return static
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
        $quote = $this->quotingCharacter;
        $divider = $this->identifierDivider;
        return $quote . str_replace($divider, $quote . $divider . $quote, $identifier) . $quote;
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
     * @throws Exception
     */
    public function describe($table)
    {
        throw new UnsupportedDriver('Not supported for this driver');
    }

    /**
     * @param Entity[] $entities
     * @return bool
     * @throws Exception\InvalidArgument
     */
    protected static function assertSameType(array $entities)
    {
        if (count($entities) < 2) {
            return true;
        }

        $type = get_class(reset($entities));
        foreach ($entities as $i => $entity) {
            if (get_class($entity) !== $type) {
                throw new Exception\InvalidArgument(sprintf('$entities[%d] is not from the same type', $i));
            }
        }

        return true;
    }

    /**
     * Insert $entities into database
     *
     * The entities have to be from same type otherwise a InvalidArgument will be thrown.
     *
     * @param Entity ...$entities
     * @return bool
     * @throws Exception\InvalidArgument
     */
    public function insert(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }
        static::assertSameType($entities);
        $insert = $this->buildInsertStatement(...$entities);
        $this->entityManager->getConnection()->query($insert);
        return true;
    }

    /**
     * Insert $entities and update with default values from database
     *
     * The entities have to be from same type otherwise a InvalidArgument will be thrown.
     *
     * @param Entity ...$entities
     * @return bool
     * @throws Exception\InvalidArgument
     */
    public function insertAndSync(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }
        self::assertSameType($entities);
        $this->insert(...$entities);
        $this->syncInserted(...$entities);
        return true;
    }

    /**
     * Insert $entities and sync with auto increment primary key
     *
     * The entities have to be from same type otherwise a InvalidArgument will be thrown.
     *
     * @param Entity ...$entities
     * @return int|bool
     * @throws UnsupportedDriver
     * @throws Exception\InvalidArgument
     */
    public function insertAndSyncWithAutoInc(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }
        self::assertSameType($entities);
        throw new UnsupportedDriver('Auto incremented column for this driver is not supported');
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
     * @param Entity[] $entities
     * @return string
     */
    protected function buildInsertStatement(Entity $entity, Entity ...$entities)
    {
        array_unshift($entities, $entity);
        $cols = [];
        $rows = [];
        foreach ($entities as $entity) {
            $data = $entity->getData();
            $cols = array_unique(array_merge($cols, array_keys($data)));
            $rows[] = $data;
        }

        $cols = array_combine($cols, array_map([$this, 'escapeIdentifier'], $cols));

        $statement = 'INSERT INTO ' . $this->escapeIdentifier($entity::getTableName()) . ' ' .
                     '(' . implode(',', $cols) . ') VALUES ';

        $statement .= implode(',', array_map(function ($values) use ($cols) {
            $result = [];
            foreach ($cols as $key => $col) {
                $result[] = isset($values[$key]) ? $this->escapeValue($values[$key]) : $this->escapeNULL();
            }
            return '(' . implode(',', $result) . ')';
        }, $rows));

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
     * Sync the $entities after insert
     *
     * @param Entity ...$entities
     */
    protected function syncInserted(Entity ...$entities)
    {
        $entity = reset($entities);
        $vars = $entity::getPrimaryKeyVars();
        $cols = array_map([$entity, 'getColumnName'], $vars);
        $primary = array_combine($vars, $cols);

        $query = "SELECT * FROM " . $this->escapeIdentifier($entity::getTableName()) . " WHERE ";
        $query .= count($cols) > 1 ?
            '(' . implode(',', array_map([$this, 'escapeIdentifier'], $cols)) . ')' :
            $this->escapeIdentifier($cols[0]);
        $query .= ' IN (';
        $pKeys = [];
        foreach ($entities as $entity) {
            $pKey = array_map([$this, 'escapeValue'], $entity->getPrimaryKey());
            $pKeys[] = count($cols) > 1 ? '(' . implode(',', $pKey) . ')' : reset($pKey);
        }
        $query .= implode(',', $pKeys) . ')';

        $statement = $this->entityManager->getConnection()->query($query);
        $left = $entities;
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            foreach ($left as $k => $entity) {
                foreach ($primary as $var => $col) {
                    if ($entity->$var != $row[$col]) {
                        continue 2;
                    }
                }

                $this->entityManager->map($entity, true);
                $entity->setOriginalData($row);
                $entity->reset();
                unset($left[$k]);
                break;
            }
        }
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

        if (($pos = strpos($type, '(')) !== false && $pos > 0) {
            $type = substr($type, 0, $pos);
        }

        return trim($type);
    }
}
