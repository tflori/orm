<?php

namespace ORM\Dbal;

use ORM\Entity;
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
        'integer'          => Type\Number::class,
        'smallint'         => Type\Number::class,
        'bigint'           => Type\Number::class,
        'numeric'          => Type\Number::class,
        'real'             => Type\Number::class,
        'double precision' => Type\Number::class,
        'money'            => Type\Number::class,

        'character varying' => Type\VarChar::class,
        'character'         => Type\VarChar::class,

        'text' => Type\Text::class,

        'date'                        => Type\DateTime::class,
        'timestamp without time zone' => Type\DateTime::class,
        'timestamp with time zone'    => Type\DateTime::class,
        'time without time zone'      => Type\Time::class,
        'time with time zone'         => Type\Time::class,

        'json'    => Type\Json::class,
        'boolean' => Type\Boolean::class,
    ];

    protected $booleanTrue  = 'true';
    protected $booleanFalse = 'false';

    public function insert(Entity $entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo       = $this->entityManager->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $statement .= ' RETURNING ' . $entity::getColumnName($entity::getPrimaryKeyVars()[0]);
            $result    = $pdo->query($statement);
            $this->updateAutoincrement($entity, $result->fetchColumn());
        } else {
            $pdo->query($statement);
        }

        return $this->entityManager->sync($entity, true);
    }

    public function describe($schemaTable)
    {
        $table = explode($this->identifierDivider, $schemaTable);
        list($schema, $table) = count($table) === 2 ? $table : [ 'public', $table[0] ];

        $query = new QueryBuilder('INFORMATION_SCHEMA.COLUMNS', '', $this->entityManager);
        $query->where('table_name', $table)->andWhere('table_schema', $schema);
        $query->columns([
            'column_name', 'column_default', 'data_type', 'is_nullable', 'character_maximum_length',
            'datetime_precision'
        ]);

        $result     = $this->entityManager->getConnection()->query($query->getQuery());
        $rawColumns = $result->fetchAll(PDO::FETCH_ASSOC);
        if (count($rawColumns) === 0) {
            throw new Exception('Unknown table ' . $schemaTable);
        }

        $cols = array_map(
            function ($columnDefinition) {
                if (isset(static::$typeMapping[$columnDefinition['data_type']])) {
                    $columnDefinition['type'] = static::$typeMapping[$columnDefinition['data_type']];
                }
                return new Column($this, $columnDefinition);
            },
            $rawColumns
        );

        return new Table($cols);
    }
}
