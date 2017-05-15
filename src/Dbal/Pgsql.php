<?php

namespace ORM\Dbal;

use ORM\Dbal;
use ORM\Exception;
use ORM\QueryBuilder\QueryBuilder;
use PDO;

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

        $cols = [];
        foreach ($rawColumns as $rawColumn) {
            $columnDefinition = $this->normalizeColumnDefinition($rawColumn);
            $cols[] = $this->columnFactory($columnDefinition, $this->getType($columnDefinition));
        }

        return $cols;
    }

    protected function normalizeColumnDefinition($rawColumn)
    {
        $definition = [];
        $definition['data_type'] = $rawColumn['data_type'];
        $definition['column_name'] = $rawColumn['column_name'];
        $definition['is_nullable'] = $rawColumn['is_nullable'] === 'YES';
        $definition['column_default'] = $rawColumn['column_default'];
        $definition['character_maximum_length'] = $rawColumn['character_maximum_length'];
        $definition['datetime_precision'] = $rawColumn['datetime_precision'];

        return $definition;
    }
}
