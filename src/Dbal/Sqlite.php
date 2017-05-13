<?php

namespace ORM\Dbal;

use ORM\Dbal;
use ORM\Exception;

class Sqlite extends Dbal
{
    protected static $typeMapping = [
        'integer' => Type\Integer::class,
        'int' => Type\Integer::class,

        'double' => Type\Double::class,
        'real' => Type\Double::class,
        'float' => Type\Double::class,
        'numeric' => Type\Double::class,
        'decimal' => Type\Double::class,

        'varchar' => Type\VarChar::class,
        'character' => Type\VarChar::class,

        'text' => Type\Text::class,

        'boolean' => Type\Boolean::class,
        'json' => Type\Json::class,

        'datetime' => Type\DateTime::class,
        'date' => Type\DateTime::class,
        'time' => Type\Time::class,
    ];

    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo = $this->em->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $pdo->query($statement);
            return $pdo->lastInsertId();
        }

        $pdo->query($statement);
        $this->em->sync($entity, true);
        return true;
    }

    public function describe($schemaTable)
    {
        $table = explode(static::$identifierDivider, $schemaTable);
        list($schema, $table) = count($table) === 2 ? $table : [null, $table[0]];
        $schema = $schema !== null ? $this->escapeIdentifier($schema) . '.' : '';

        $result = $this->em->getConnection()->query(
            'PRAGMA ' . $schema . 'table_info(' . $this->escapeIdentifier($table) . ')'
        );
        $rawColumns = $result->fetchAll(\PDO::FETCH_ASSOC);

        if (count($rawColumns) === 0) {
            throw new Exception('Unknown table '  . $table);
        }

        $cols = [];
        foreach ($rawColumns as $i => $rawColumn) {
            $type = strtolower($rawColumn['type']);

            // remove size for mapping
            if (($p = strpos($type, '(')) !== false && $p > 0) {
                $type = substr($type, 0, $p);
            }

            $class  = isset(static::$typeMapping[$type]) ? static::$typeMapping[$type] : Dbal\Type\Text::class;

            $hasDefault = $rawColumn['dflt_value'] !== null;

            if (!$hasDefault && $rawColumn['type'] === 'integer' && $rawColumn['pk'] === '1') {
                $multiplePrimaryKey = false;
                if ($i < count($rawColumns) - 1) {
                    for ($j = $i+1; $j < count($rawColumns); $j++) {
                        if ($rawColumns[$j]['pk'] !== '0') {
                            $multiplePrimaryKey = true;
                            break;
                        }
                    }
                }
                if (!$multiplePrimaryKey) {
                    $hasDefault = true;
                }
            }

            $cols[]     = new Column(
                $rawColumn['name'],
                new $class,
                $hasDefault,
                $rawColumn['notnull'] === '0'
            );
        }

        return $cols;
    }
}
