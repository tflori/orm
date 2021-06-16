<?php

namespace ORM\Dbal;

use ORM\Dbal\QueryLanguage\CompositeInValuesExpression;
use ORM\Dbal\QueryLanguage\DeleteStatement;
use ORM\Dbal\QueryLanguage\InsertStatement;
use ORM\Dbal\QueryLanguage\UpdateStatement;
use ORM\Entity;
use ORM\EntityManager;
use ORM\Exception;
use ORM\Exception\UnsupportedDriver;
use PDO;

/**
 * Base class for database abstraction
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Dbal
{
    use Escaping;
    use UpdateStatement;
    use InsertStatement;
    use CompositeInValuesExpression;
    use DeleteStatement {
        UpdateStatement::buildWhereClause insteadof DeleteStatement;
    }

    /** @var array */
    protected static $typeMapping = [];

    /** @var EntityManager */
    protected $entityManager;

    /** Number of opened transactions
     * @var int */
    protected $transactionCounter = 0;

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
     * @return $this
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
     * Begin a transaction or create a savepoint
     *
     * @return bool
     */
    public function beginTransaction()
    {
        $conn = $this->entityManager->getConnection();
        if (!$this->transactionCounter) {
            $started = $conn->beginTransaction();
            !$started ?: $this->transactionCounter++;
            return $started;
        }
        $created = $conn->exec('SAVEPOINT transaction' . ($this->transactionCounter + 1));
        $created === false ?: $this->transactionCounter++;
        return $created !== false;
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
     */
    public function commit($all = false)
    {
        $conn = $this->entityManager->getConnection();
        if (!$conn->inTransaction() || $this->transactionCounter === 0) {
            return true; // or false?
        }
        $committed = $this->transactionCounter > 1 && !$all || $conn->commit();
        !$committed ?: ($all ? $this->transactionCounter = 0 : $this->transactionCounter--);
        return $committed;
    }

    /**
     * Rollback the current transaction or save point
     *
     * @return bool
     */
    public function rollback()
    {
        $conn = $this->entityManager->getConnection();
        if (!$conn->inTransaction() || $this->transactionCounter === 0) {
            return false; // or true?
        }
        $rolledBack = $this->transactionCounter > 1 ?
            $conn->exec('ROLLBACK TO transaction' . $this->transactionCounter) !== false :
            $conn->rollBack();
        !$rolledBack ?: $this->transactionCounter--;
        return $rolledBack;
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
        throw new UnsupportedDriver('Describe is not supported by this driver');
    }

    public function insert($table, array ...$rows)
    {
        if (count($rows) === 0) {
            return 0;
        }

        $insert = $this->buildInsert($table, $rows);
        $statement = $this->entityManager->getConnection()->query($insert);
        return $statement->rowCount();
    }

    /**
     * Insert $entities into database
     *
     * The entities have to be from same type otherwise a InvalidArgument will be thrown.
     *
     * @param Entity ...$entities
     * @return bool
     */
    public function insertEntities(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }

        static::assertSameType($entities);
        $this->insert($entities[0]::getTableName(), ...array_map(function (Entity $entity) {
            return $entity->getData();
        }, $entities));
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

        $this->insertEntities(...$entities);
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
     * Update $table using $where to set $updates
     *
     * Simple usage: `update('table', ['id' => 23], ['name' => 'John Doe'])`
     *
     * For advanced queries with parenthesis, joins (if supported from your DBMS) etc. use QueryBuilder:
     *
     * ```php
     * $em->query('table')
     *  ->where('birth_date', '>', EM::raw('DATE_SUB(NOW(), INTERVAL 18 YEARS)'))
     *  ->update(['teenager' => true]);
     * ```
     *
     * @param string $table The table to update
     * @param array $where An array of where conditions
     * @param array $updates An array of columns to update
     * @param array $joins For internal use from query builder only
     * @return int The number of affected rows
     * @throws UnsupportedDriver
     */
    public function update($table, array $where, array $updates, array $joins = [])
    {
        if (!empty($joins)) {
            throw new UnsupportedDriver('Updates with joins are not supported by this driver');
        }

        $query = $this->buildUpdateStatement($table, $where, $updates);
        $statement = $this->entityManager->getConnection()->query($query);
        return $statement->rowCount();
    }

    /**
     * Update $entity in database and returns success
     *
     * @param Entity $entity
     * @return bool
     * @internal
     */
    public function updateEntity(Entity $entity)
    {
        $data       = $entity->getData();
        $primaryKey = $entity->getPrimaryKey();

        $where = [];
        foreach ($primaryKey as $attribute => $value) {
            $col = $entity::getColumnName($attribute);
            $where[$col] = $value;
            if (isset($data[$col])) {
                unset($data[$col]);
            }
        }

        $this->update($entity::getTableName(), $where, $data);
        return $this->entityManager->sync($entity, true);
    }

    /**
     * Delete rows from $table using $where conditions
     *
     * Where conditions can be an array of key => value pairs to check for equality or an array of expressions.
     *
     * Examples:
     * `$dbal->delete('someTable', ['id' => 23])`
     * `$dbal->delete('user', ['name = \'john\'', 'OR email=\'john.doe@example.com\''])`
     *
     * Tip: Use the query builder to construct where conditions:
     * `$em->query('user')->where('name', 'john')->orWhere('email', '...')->delete();`
     *
     * @param string $table The table where to delete rows
     * @param array $where An array of where conditions
     * @return int The number of deleted rows
     */
    public function delete($table, array $where)
    {
        $query = $this->buildDeleteStatement($table, $where);
        $statement = $this->entityManager->getConnection()->query($query);
        return $statement->rowCount();
    }

    /**
     * Delete $entity from database
     *
     * This method does not delete from the map - you can still receive the entity via fetch.
     *
     * @param Entity $entity
     * @return bool
     */
    public function deleteEntity(Entity $entity)
    {
        $primaryKey = $entity->getPrimaryKey();
        $where = [];
        foreach ($primaryKey as $attribute => $value) {
            $col = $entity::getColumnName($attribute);
            $where[$col] = $value;
        }

        $this->delete($entity::getTableName(), $where);
        return true;
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
        $cols = array_map([$this, 'escapeIdentifier'], $cols);

        $query = $this->entityManager->query($this->escapeIdentifier($entity::getTableName()))
            ->whereIn($cols, array_map(function (Entity $entity) {
                return $entity->getPrimaryKey();
            }, $entities))
            ->setFetchMode(PDO::FETCH_ASSOC);

        $left = $entities;
        while ($row = $query->one()) {
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
