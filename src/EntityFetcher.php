<?php

namespace ORM;

use ORM\Exceptions\NotJoined;
use ORM\QueryBuilder\ParenthesisInterface;
use ORM\QueryBuilder\QueryBuilder;
use ORM\QueryBuilder\QueryBuilderInterface;

/**
 * Fetch entities from database
 *
 * If you need more specific queries you write them yourself. If you need just more specific where clause you can pass
 * them to the *where() methods.
 *
 * Supported:
 *  - joins with on clause (and alias)
 *  - joins with using (and alias)
 *  - where conditions
 *  - parenthesis
 *  - order by one or more columns / expressions
 *  - group by one or more columns / expressions
 *  - limit and offset
 *  - modifiers
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class EntityFetcher extends QueryBuilder
{
    /** The entity manager where entities get stored
     * @var EntityManager */
    protected $entityManager;

    /** The entity class that we want to fetch
     * @var string|Entity */
    protected $class;

    /** The result object from PDO
     * @var \PDOStatement */
    protected $result;

    /** The query to execute (overwrites other settings)
     * @var string|QueryBuilderInterface */
    protected $query;

    /** The class to alias mapping and vise versa
     * @var string[][] */
    protected $classMapping = [
        'byClass' => [],
        'byAlias' => [],
    ];

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * Constructor
     *
     * @param EntityManager $entityManager EntityManager where to store the fetched entities
     * @param Entity|string $class         Class to fetch
     * @throws Exceptions\InvalidConfiguration
     * @throws Exceptions\InvalidName
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;

        $this->tableName = $entityManager->escapeIdentifier($class::getTableName());
        $this->alias = 't0';
        $this->columns = ['t0.*'];
        $this->modifier = ['DISTINCT'];

        $this->classMapping['byClass'][$class] = 't0';
        $this->classMapping['byAlias']['t0'] = $class;
    }

    /** @return self
     * @internal
     */
    public function columns(array $columns = null)
    {
        return $this;
    }

    /** @return self
     * @internal
     */
    public function column($column, $args = [], $alias = '')
    {
        return $this;
    }

    /**
     * Replaces questionmarks in $expression with $args
     *
     * Additionally this method replaces "ClassName::var" with "alias.col" and "alias.var" with "alias.col" if
     * $translateCols is true (default).
     *
     * @param string      $expression    Expression with placeholders
     * @param array|mixed $args          Argument(s) to insert
     * @param bool        $translateCols Whether or not column names should be translated
     * @return string
     * @throws Exceptions\NoConnection
     * @throws Exceptions\NotScalar
     */
    protected function convertPlaceholders($expression, $args, $translateCols = true)
    {
        if ($translateCols) {
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
                            return $match[0];
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
                    return $match['b'] . $this->entityManager->escapeIdentifier(
                        $alias . '.' . $class::getColumnName($match['column'])
                    ) . $match['a'];
                },
                $expression
            );
        }

        return parent::convertPlaceholders($expression, $args);
    }

    /**
     * Common implementation for *Join methods
     *
     * Additionally this method replaces class name with table name and forces an alias.
     *
     * @param string      $join       The join type (e. g. `LEFT JOIN`)
     * @param string      $class      Class to join
     * @param string      $expression Expression to use in on clause or single column for USING
     * @param string      $alias      Alias for the table
     * @param array|mixed $args       Arguments to use in $expression
     * @param bool        $empty      Create an empty join (without USING and ON)
     * @return EntityFetcher|ParenthesisInterface
     * @throws Exceptions\InvalidConfiguration
     * @throws Exceptions\InvalidName
     * @throws Exceptions\NoConnection
     * @throws Exceptions\NotScalar
     * @internal
     */
    protected function createJoin($join, $class, $expression, $alias, $args, $empty)
    {
        if (class_exists($class)) {
            /** @var Entity|string $class */
            $tableName = $this->entityManager->escapeIdentifier($class::getTableName());
            $alias = $alias ?: 't' . count($this->classMapping['byAlias']);

            $this->classMapping['byClass'][$class] = $alias;
            $this->classMapping['byAlias'][$alias] = $class;
        } else {
            $tableName = $class;
        }

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
     * If there is no more entity in the result set it returns null.
     *
     * @return Entity
     * @throws Exceptions\IncompletePrimaryKey
     * @throws Exceptions\InvalidConfiguration
     * @throws Exceptions\NoConnection
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
        $newEntity = new $c($data, $this->entityManager, true);
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
     * When no $limit is set it fetches all entities in result set.
     *
     * @param int $limit Maximum number of entities to fetch
     * @return Entity[]
     * @throws Exceptions\IncompletePrimaryKey
     * @throws Exceptions\InvalidConfiguration
     * @throws Exceptions\NoConnection
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
     * Query database and return result
     *
     * Queries the database with current query and returns the resulted PDOStatement.
     *
     * If query failed it returns false. It also stores this failed result and to change the query afterwards will not
     * change the result.
     *
     * @return \PDOStatement
     * @throws Exceptions\NoConnection
     */
    private function getStatement()
    {
        if ($this->result === null) {
            $this->result = $this->entityManager->getConnection()->query($this->getQuery());
        }
        return $this->result;
    }

    /** {@inheritdoc} */
    public function getQuery()
    {
        if ($this->query) {
            return $this->query instanceof  QueryBuilderInterface ? $this->query->getQuery() : $this->query;
        }
        return parent::getQuery();
    }

    /**
     * Set a raw query or use different QueryBuilder
     *
     * For easier use and against sql injection it allows question mark placeholders.
     *
     * @param string|QueryBuilderInterface $query Raw query string or a QueryBuilderInterface
     * @param array                        $args  The arguments for placeholders
     * @return $this
     * @throws Exceptions\NoConnection
     * @throws Exceptions\NotScalar
     */
    public function setQuery($query, $args = null)
    {
        if (!$query instanceof QueryBuilderInterface) {
            $query = $this->convertPlaceholders($query, $args, false);
        }

        $this->query = $query;
        return $this;
    }
}
