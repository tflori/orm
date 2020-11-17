<?php

namespace ORM\Dbal;

use ORM\Entity;
use ORM\Exception;
use PDO;

/**
 * Database abstraction for SQLite databases
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Sqlite extends Dbal
{
    protected static $typeMapping = [
        'integer' => Type\Number::class,
        'int'     => Type\Number::class,
        'double'  => Type\Number::class,
        'real'    => Type\Number::class,
        'float'   => Type\Number::class,
        'numeric' => Type\Number::class,
        'decimal' => Type\Number::class,

        'varchar'   => Type\VarChar::class,
        'character' => Type\VarChar::class,

        'text' => Type\Text::class,

        'boolean' => Type\Boolean::class,
        'json'    => Type\Json::class,

        'datetime' => Type\DateTime::class,
        'date'     => Type\DateTime::class,
        'time'     => Type\Time::class,
    ];

    public function insertAndSyncWithAutoInc(Entity ...$entities)
    {
        if (count($entities) === 0) {
            return false;
        }
        static::assertSameType($entities);

        $entity = reset($entities);
        $pdo = $this->entityManager->getConnection();
        $table = $this->escapeIdentifier($entity::getTableName());
        $pKey = $this->escapeIdentifier($entity::getColumnName($entity::getPrimaryKeyVars()[0]));
        $pdo->beginTransaction();
        $pdo->query($this->buildInsert($entities[0]::getTableName(), array_map(function (Entity $entity) {
            return $entity->getData();
        }, $entities)));
        $rows = $pdo->query('SELECT * FROM ' . $table . ' WHERE ' . $pKey . ' <= ' . $pdo->lastInsertId() .
                            ' ORDER BY ' . $pKey . ' DESC LIMIT ' . count($entities))
            ->fetchAll(PDO::FETCH_ASSOC);
        $pdo->commit();

        /** @var Entity $entity */
        foreach (array_reverse($entities) as $key => $entity) {
            $entity->setOriginalData($rows[$key]);
            $entity->reset();
            $this->entityManager->map($entity, true);
        }

        return true;
    }

    public function describe($schemaTable)
    {
        $table = explode($this->identifierDivider, $schemaTable);
        list($schema, $table) = count($table) === 2 ? $table : [ null, $table[ 0 ] ];
        $schema = $schema !== null ? $this->escapeIdentifier($schema) . '.' : '';

        $result     = $this->entityManager->getConnection()->query(
            'PRAGMA ' . $schema . 'table_info(' . $this->escapeIdentifier($table) . ')'
        );
        $rawColumns = $result->fetchAll(PDO::FETCH_ASSOC);

        if (count($rawColumns) === 0) {
            throw new Exception('Unknown table ' . $table);
        }

        $compositeKey = $this->hasCompositeKey($rawColumns);

        $cols = array_map(function ($rawColumn) use ($compositeKey) {
            $columnDefinition = $this->normalizeColumnDefinition($rawColumn, $compositeKey);
            return new Column($this, $columnDefinition);
        }, $rawColumns);

        return new Table($cols);
    }

    /**
     * Checks $rawColumns for a multiple primary key
     *
     * @param array $rawColumns
     * @return bool
     */
    protected function hasCompositeKey($rawColumns)
    {
        return count(array_filter(array_map(
            function ($rawColumn) {
                return $rawColumn[ 'pk' ];
            },
            $rawColumns
        ))) > 1;
    }

    /**
     * Normalize a column definition
     *
     * The column definition from "PRAGMA table_info(<table>)" is to special as useful. Here we normalize it to a more
     * ANSI-SQL style.
     *
     * @param array $rawColumn
     * @param bool  $compositeKey
     * @return array
     */
    protected function normalizeColumnDefinition($rawColumn, $compositeKey = false)
    {
        $definition = [];

        $definition['data_type'] = $this->normalizeType($rawColumn['type']);
        $definition['type'] = isset(static::$typeMapping[$definition['data_type']]) ?
            static::$typeMapping[$definition['data_type']] : null;

        $definition['column_name']              = $rawColumn['name'];
        $definition['is_nullable']              = $rawColumn['notnull'] === '0';
        $definition['column_default']           = $rawColumn['dflt_value'];
        $definition['character_maximum_length'] = null;
        $definition['datetime_precision']       = null;

        if (in_array($definition['data_type'], ['varchar', 'char'])) {
            $definition['character_maximum_length'] = $this->extractParenthesis($rawColumn['type']);
        } elseif (in_array($definition['data_type'], ['datetime', 'timestamp', 'time'])) {
            $definition['datetime_precision'] = $this->extractParenthesis($rawColumn['type']);
        } elseif ($definition['data_type'] === 'integer' && !$definition['column_default'] &&
                  $rawColumn['pk'] === '1' && !$compositeKey
        ) {
            $definition['column_default'] = 'sequence(rowid)';
        }

        return $definition;
    }
}
