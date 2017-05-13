<?php

namespace ORM\Dbal;

use ORM\Dbal;
use ORM\Exception;

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

        try {
            $result = $this->em->getConnection()->query('DESCRIBE ' . $this->escapeIdentifier($table));
        } catch (\PDOException $exception) {
            throw new Exception('Unknown table ' . $table, 0, $exception);
        }

        while ($rawColumn = $result->fetch(\PDO::FETCH_ASSOC)) {
            $type = $this->normlizeType($rawColumn['Type']);
            $class  = isset(static::$typeMapping[$type]) ? static::$typeMapping[$type] : Dbal\Type\Text::class;

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
