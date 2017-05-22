<?php

namespace ORM\Dbal;

use ORM\Exception;
use ORM\QueryBuilder\QueryBuilder;
use PDO;

/**
 * Database abstraction for PostgreSQL databases
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Pgsql extends Dbal
{
    protected static $typeMapping = [
        'integer' => Type\Integer::class,
        'smallint' => Type\Integer::class,
        'bigint' => Type\Integer::class,

        'numeric' => Type\Double::class,
        'real' => Type\Double::class,
        'double precision' => Type\Double::class,
        'money' => Type\Double::class,

        'character varying' => Type\VarChar::class,
        'character' => Type\VarChar::class,

        'text' => Type\Text::class,

        'date' => Type\DateTime::class,
        'timestamp without time zone' => Type\DateTime::class,
        'timestamp with time zone' => Type\DateTime::class,
        'time without time zone' => Type\Time::class,
        'time with time zone' => Type\Time::class,

        'json' => Type\Json::class,
        'boolean' => Type\Boolean::class,
    ];

    protected static $booleanTrue = 'true';
    protected static $booleanFalse = 'false';

    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo = $this->em->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $statement .= ' RETURNING ' . $entity::getColumnName($entity::getPrimaryKeyVars()[0]);
            $result = $pdo->query($statement);
            return $result->fetchColumn();
        }

        $pdo->query($statement);
        $this->em->sync($entity, true);
        return true;
    }

    public function describe($schemaTable)
    {
        $table = explode(static::$identifierDivider, $schemaTable);
        list($schema, $table) = count($table) === 2 ? $table : ['public', $table[0]];

        $query = new QueryBuilder('INFORMATION_SCHEMA.COLUMNS');
        $query->where('table_name', $table)->andWhere('table_schema', $schema);
        $query->columns([
            'column_name', 'column_default', 'data_type', 'is_nullable', 'character_maximum_length',
            'datetime_precision'
        ]);

        $result = $this->em->getConnection()->query($query->getQuery());
        $rawColumns = $result->fetchAll(PDO::FETCH_ASSOC);
        if (count($rawColumns) === 0) {
            throw new Exception('Unknown table '  . $schemaTable);
        }

        $cols = array_map(function ($columnDefinition) {
            return Column::factory($columnDefinition, $this->getType($columnDefinition));
        }, $rawColumns);

        return $cols;
    }

    protected function getType($columnDefinition)
    {
        if (isset(static::$typeMapping[$columnDefinition['data_type']])) {
            $factory = [static::$typeMapping[$columnDefinition['data_type']], 'factory'];
            return call_user_func($factory, $this, $columnDefinition);
        }

        return parent::getType($columnDefinition);
    }
}
