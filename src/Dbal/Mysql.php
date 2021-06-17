<?php

namespace ORM\Dbal;

use ORM\Dbal\QueryLanguage\CompositeInExpression;
use ORM\Dbal\QueryLanguage\UpdateJoinStatement;
use ORM\Entity;
use ORM\Exception;
use PDO;
use PDOException;

/**
 * Database abstraction for MySQL databases
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Mysql extends Dbal
{
    use UpdateJoinStatement;
    use CompositeInExpression;

    protected static $typeMapping = [
        'tinyint'   => Type\Number::class,
        'smallint'  => Type\Number::class,
        'mediumint' => Type\Number::class,
        'int'       => Type\Number::class,
        'bigint'    => Type\Number::class,
        'decimal'   => Type\Number::class,
        'float'     => Type\Number::class,
        'double'    => Type\Number::class,

        'varchar' => Type\VarChar::class,
        'char'    => Type\VarChar::class,

        'text'       => Type\Text::class,
        'tinytext'   => Type\Text::class,
        'mediumtext' => Type\Text::class,
        'longtext'   => Type\Text::class,

        'datetime'  => Type\DateTime::class,
        'date'      => Type\DateTime::class,
        'timestamp' => Type\DateTime::class,

        'time' => Type\Time::class,
        'enum' => Type\Enum::class,
        'set'  => Type\Set::class,
        'json' => Type\Json::class,
    ];

    public function insertAndSyncWithAutoInc(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }
        static::assertSameType($entities);

        $entity = reset($entities);
        $pdo = $this->entityManager->getConnection();
        $this->beginTransaction();
        try {
            $pdo->query($this->buildInsert($entity::getTableName(), array_map(function (Entity $entity) {
                return $entity->getData();
            }, $entities)));
            $this->syncInsertedWithAutoInc(...$entities);
            $this->commit();
        } catch (PDOException $exception) {
            $this->rollback();
            throw $exception;
        }

        return true;
    }

    /**
     * Synchronize inserted $entities
     *
     * This method expects that the inserted $entities where the last inserted entities and using
     * an auto incremented primary key.
     *
     * @param Entity ...$entities
     */
    protected function syncInsertedWithAutoInc(Entity ...$entities)
    {
        $entity = reset($entities);
        $table = $this->escapeIdentifier($entity::getTableName());
        $pKey = $this->escapeIdentifier($entity::getColumnName($entity::getPrimaryKeyVars()[0]));
        $pdo = $this->entityManager->getConnection();
        $rows = $pdo->query(
            'SELECT * FROM ' . $table . ' WHERE ' . $pKey . ' >= LAST_INSERT_ID()'
        )->fetchAll(PDO::FETCH_ASSOC);

        /** @var Entity $entity */
        foreach (array_values($entities) as $key => $entity) {
            $entity->setOriginalData($rows[$key]);
            $entity->reset();
            $this->entityManager->map($entity, true);
        }
    }

    public function describe($table)
    {
        try {
            $result = $this->entityManager->getConnection()->query('DESCRIBE ' . $this->escapeIdentifier($table));
        } catch (PDOException $exception) {
            throw new Exception('Unknown table ' . $table, 0, $exception);
        }

        $cols = [];
        while ($rawColumn = $result->fetch(PDO::FETCH_ASSOC)) {
            $cols[] = new Column($this, $this->normalizeColumnDefinition($rawColumn));
        }

        return new Table($cols);
    }

    public function update($table, array $where, array $updates, array $joins = [])
    {
        $query = $this->buildUpdateJoinStatement($table, $where, $updates, $joins);
        $statement = $this->entityManager->getConnection()->query($query);
        return $statement->rowCount();
    }

    /**
     * Normalize a column definition
     *
     * The column definition from "DESCRIBE <table>" is to special as useful. Here we normalize it to a more
     * ANSI-SQL style.
     *
     * @param array $rawColumn
     * @return array
     */
    protected function normalizeColumnDefinition($rawColumn)
    {
        $definition = [];

        $definition['data_type'] = $this->normalizeType($rawColumn['Type']);
        $definition['type'] = isset(static::$typeMapping[$definition['data_type']]) ?
            static::$typeMapping[$definition['data_type']] : null;

        $definition['column_name'] = $rawColumn['Field'];
        $definition['is_nullable'] = $rawColumn['Null'] === 'YES';
        $definition['character_maximum_length'] = null;
        $definition['datetime_precision'] = null;
        $definition['column_default'] = $rawColumn['Default'] === null && $rawColumn['Extra'] === 'auto_increment' ?
            'sequence(AUTO_INCREMENT)' : $rawColumn['Default'];

        if (in_array($definition['data_type'], ['varchar', 'char'])) {
            $definition['character_maximum_length'] = $this->extractParenthesis($rawColumn['Type']);
        } elseif (in_array($definition['data_type'], ['datetime', 'timestamp', 'time'])) {
            $definition['datetime_precision'] = $this->extractParenthesis($rawColumn['Type']);
        } elseif (in_array($definition['data_type'], ['set', 'enum'])) {
            $definition['enumeration_values'] = $this->extractParenthesis($rawColumn['Type']);
        }

        return $definition;
    }
}
