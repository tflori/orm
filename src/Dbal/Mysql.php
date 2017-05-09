<?php

namespace ORM\Dbal;

use ORM\Dbal;

class Mysql extends Dbal
{
    protected static $typeMapping = [
        'tinyint' => Type\Integer::class,
        'smallint' => Type\Integer::class,
        'mediumint' => Type\Integer::class,
        'int' => Type\Integer::class,
        'bigint' => Type\Integer::class,

        'decimal' => Type\Double::class,
        'float' => Type\Double::class,
        'double' => Type\Double::class,

        'varchar' => Type\VarChar::class,
        'char' => Type\VarChar::class,

        'text' => Type\Text::class,
        'tinytext' => Type\Text::class,
        'mediumtext' => Type\Text::class,
        'longtext' => Type\Text::class,

        'datetime' => Type\DateTime::class,
        'date' => Type\DateTime::class,
        'timestamp' => Type\DateTime::class,

        'time' => Type\Time::class,

        'enum' => Type\Enum::class,

        'set' => Type\Set::class,

        'json' => Type\Json::class,
    ];

    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo = $this->em->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $pdo->query($statement);
            return $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
        }

        $pdo->query($statement);
        $this->em->sync($entity, true);
        return true;
    }

    public function describe($table)
    {
        $cols = [];

        $result = $this->em->getConnection()->query('DESCRIBE ' . $this->escapeIdentifier($table));
        while ($rawColumn = $result->fetch(\PDO::FETCH_ASSOC)) {
            $type = $rawColumn['Type'];

            // remove size for mapping
            if (($p = strpos($type, '(')) !== false && $p > 0) {
                $type = substr($type, 0, $p);
            }

            $class  = static::$typeMapping[$type];
            $cols[] = new Column(
                $rawColumn['Field'],
                new $class,
                $rawColumn['Default'] !== null || $rawColumn['Extra'] === 'auto_increment',
                $rawColumn['Null'] === 'YES'
            );
        }

        return $cols;
    }
}
