<?php

namespace ORM;

use ORM\Exceptions\NotJoined;
use ORM\QueryBuilder\QueryBuilder;

/**
 * Class EntityFetcher
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class EntityFetcher extends QueryBuilder
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var string|Entity */
    protected $class;

    /** @var \PDOStatement */
    protected $result;

    /** @var string */
    protected $query;

    /** @var array */
    protected $classMapping = [
        'byClass' => [],
        'byAlias' => [],
    ];

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * EntityFetcher constructor.
     *
     * @param EntityManager $entityManager
     * @param Entity|string $class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;

        $this->tableName = $class::getTableName();
        $this->alias = 't0';
        $this->columns = ['t0.*'];
        $this->modifier = ['DISTINCT'];

        $this->classMapping['byClass'][$class] = 't0';
        $this->classMapping['byAlias']['t0'] = $class;
    }

    /**
     * Columns can't be changed
     *
     * @param array|null $columns
     * @return $this
     */
    public function columns(array $columns = null)
    {
        return $this;
    }

    /**
     * Columns can't be changed
     *
     * @param string $column
     * @param array $args
     * @param string $alias
     * @return $this
     */
    public function column($column, $args = [], $alias = '')
    {
        return $this;
    }

    protected function convertPlaceholders($expression, $args)
    {
        $expression = preg_replace_callback(
            '/(?<b>^| |\()' .
            '((?<class>[A-Za-z_][A-Za-z0-9_\\\\]*)::|(?<alias>[A-Za-z_][A-Za-z0-9_]+)\.)?' .
            '(?<column>[A-Za-z_][A-Za-z0-9_]*)' .
            '(?<a>$| |,|\))/',
            function ($match) {
                if ($match['class']) {
                    if (!isset($this->classMapping['byClass'][$match['class']])) {
                        throw new NotJoined("Class " . $match['class'] . " not joined");
                    }
                    $class = $match['class'];
                    $alias = $this->classMapping['byClass'][$match['class']];
                } elseif ($match['alias']) {
                    if (!isset($this->classMapping['byAlias'][$match['alias']])) {
                        throw new NotJoined("Alias " . $match['alias'] . " unknown");
                    }
                    $alias = $match['alias'];
                    $class = $this->classMapping['byAlias'][$match['alias']];
                } else {
                    if ($match['column'] === strtoupper($match['column'])) {
                        return $match['b'] . $match['column'] . $match['a'];
                    }
                    $class = $this->class;
                    $alias = $this->alias;
                }

                /** @var Entity|string $class */
                return $match['b'] . $alias . '.' . $class::getColumnName($match['column']) . $match['a'];
            },
            $expression
        );

        return parent::convertPlaceholders($expression, $args);
    }


    protected function createJoin($join, $class, $expression, $alias, $args, $empty)
    {
        /** @var Entity|string $class */
        $tableName = $class::getTableName();
        $alias = $alias ?: 't' . count($this->classMapping['byAlias']);

        $this->classMapping['byClass'][$class] = $alias;
        $this->classMapping['byAlias'][$alias] = $class;

        return parent::createJoin(
            $join,
            $tableName,
            $expression,
            $alias,
            $args,
            $empty
        );
    }


    /**
     * Fetch one entity
     *
     * @return Entity
     */
    public function one()
    {
        $result = $this->getStatement();
        if (!$result) {
            return null;
        }

        $data      = $result->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $c         = $this->class;
        $newEntity = new $c($data, true);
        $entity    = $this->entityManager->map($newEntity);

        if ($newEntity !== $entity) {
            $dirty = $entity->isDirty();
            $entity->setOriginalData($data);
            if (!$dirty && $entity->isDirty()) {
                $entity->reset();
            }
        }

        return $entity;
    }

    /**
     * Fetch an array of entities
     *
     * When no $limit is set it fetches all entities in current cursor.
     *
     * @param int $limit Maximum number of entities to fetch
     * @return Entity[]
     */
    public function all($limit = 0)
    {
        $result = [];

        while ($entity = $this->one()) {
            $result[] = $entity;
            if ($limit && count($result) >= $limit) {
                break;
            }
        }

        return $result;
    }

    /**
     * @return \PDOStatement
     */
    private function getStatement()
    {
        if ($this->result === null) {
            $c            = $this->class;
            $this->result = $this->entityManager->getConnection($c::$connection)->query($this->getQuery());
        }
        return $this->result;
    }

    public function getQuery()
    {
        if ($this->query) {
            return $this->query;
        }
        return parent::getQuery();
    }

    public function setQuery($query, array $args = null)
    {
        if (is_array($args) && count($args) === substr_count($query, '?')) {
            $queryParts = explode('?', $query);
            $query = '';
            $c = $this->class;
            foreach ($queryParts as $part) {
                $query .= $part;
                if (count($args)) {
                    $query .= $this->entityManager->convertValue(array_shift($args), $c::$connection);
                }
            }
        }

        $this->query = $query;
        return $this;
    }
}
