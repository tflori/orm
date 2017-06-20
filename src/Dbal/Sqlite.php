<?php

namespace ORM\Dbal;

use ORM\Entity;
use ORM\Exception;

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

    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo       = $this->entityManager->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $pdo->query($statement);
            $this->updateAutoincrement($entity, $pdo->lastInsertId());
        } else {
            $pdo->query($statement);
        }

        return $this->entityManager->sync($entity, true);
    }

    public function describe($schemaTable)
    {
        $table = explode($this->identifierDivider, $schemaTable);
        list($schema, $table) = count($table) === 2 ? $table : [ null, $table[ 0 ] ];
        $schema = $schema !== null ? $this->escapeIdentifier($schema) . '.' : '';

        $result     = $this->entityManager->getConnection()->query(
            'PRAGMA ' . $schema . 'table_info(' . $this->escapeIdentifier($table) . ')'
        );
        $rawColumns = $result->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rawColumns) === 0) {
            throw new Exception('Unknown table ' . $table);
        }

        $hasMultiplePrimaryKey = $this->hasMultiplePrimaryKey($rawColumns);

        $cols = array_map(function ($rawColumn) use ($hasMultiplePrimaryKey) {
            $columnDefinition = $this->normalizeColumnDefinition($rawColumn, $hasMultiplePrimaryKey);
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
    protected function hasMultiplePrimaryKey($rawColumns)
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
     * @param bool  $hasMultiplePrimaryKey
     * @return array
     */
    protected function normalizeColumnDefinition($rawColumn, $hasMultiplePrimaryKey = false)
    {
        $definition = [];

        $definition['data_type'] = $this->normalizeType($rawColumn['type']);
        if (isset(static::$typeMapping[$definition['data_type']])) {
            $definition['type'] = static::$typeMapping[$definition['data_type']];
        }

        $definition['column_name']              = $rawColumn['name'];
        $definition['is_nullable']              = $rawColumn['notnull'] === '0';
        $definition['column_default']           = $rawColumn['dflt_value'];
        $definition['character_maximum_length'] = null;
        $definition['datetime_precision']       = null;

        switch ($definition['data_type']) {
            case 'varchar':
            case 'char':
                $definition['character_maximum_length'] = $this->extractParenthesis($rawColumn['type']);
                break;
            case 'datetime':
            case 'timestamp':
            case 'time':
                $definition['datetime_precision'] = $this->extractParenthesis($rawColumn['type']);
                break;
            case 'integer':
                if (!$definition['column_default'] && $rawColumn['pk'] === '1' && !$hasMultiplePrimaryKey) {
                    $definition['column_default'] = 'sequence(rowid)';
                }
                break;
        }

        return $definition;
    }
}
