<?php

namespace ORM\QueryBuilder;

use ORM\EntityManager;

/**
 * Class QueryBuilder
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class QueryBuilder extends Parenthesis implements QueryBuilderInterface
{
    /** @var string */
    protected $tableName = '';

    /** @var string */
    protected $alias = '';

    /** @var array */
    protected $columns = null;

    /** @var array */
    protected $joins = [];

    /** @var string[] */
    protected $where = [];

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var string[] */
    protected $groupBy = [];

    /** @var string[] */
    protected $orderBy = [];

    /** @var string[] */
    protected $modifier = [];

    /**
     * The default EntityManager to use to for quoting
     *
     * @var EntityManager
     */
    public static $defaultEntityManager;

    /**
     * The default connection to use for quoting
     *
     * @var string
     */
    public static $defaultConnection = 'default';

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * QueryBuilder constructor
     *
     * @param string $tableName
     * @param string $alias
     * @param EntityManager $entityManager
     * @param string $connection
     */
    public function __construct($tableName, $alias = '', EntityManager $entityManager = null, $connection = null)
    {
        $this->tableName = $tableName;
        $this->alias = $alias;
        $this->entityManager = $entityManager;
        $this->connection = $connection;
    }

    /**
     * Replaces questionmarks in $expression with $args
     *
     * @param string      $expression
     * @param array|mixed $args
     * @return string
     */
    protected function convertPlaceholders(
        $expression,
        $args
    ) {
        if (strpos($expression, '?') === false) {
            return $expression;
        }

        if (!is_array($args)) {
            $args = [$args];
        }

        $entityManager = $this->entityManager ?: static::$defaultEntityManager;
        $connection = $this->connection ?: static::$defaultConnection;

        $parts = explode('?', $expression);
        $expression = '';
        while ($part = array_shift($parts)) {
            $expression .= $part;
            if (count($args)) {
                $expression .= $entityManager->convertValue(array_shift($args), $connection);
            } elseif (count($parts)) {
                $expression .= '?';
            }
        }

        return $expression;
    }

    /** {@inheritdoc} */
    public function getWhereCondition($column, $operator = '', $value = '')
    {
        if (strpos($column, '?') !== false) {
            $expression = $column;
            $value      = $operator;
        } elseif (!$operator && !$value) {
            $expression = $column;
        } else {
            if (!$value) {
                $value = $operator;
                if (is_array($value)) {
                    $operator = 'IN';
                } else {
                    $operator = '=';
                }
            }

            $expression = $expression = $column . ' ' . $operator;

            if (in_array(strtoupper($operator), ['IN', 'NOT IN'], true) && is_array($value)) {
                $expression .= ' (?' . str_repeat(',?', count($value) - 1) . ')';
            } else {
                $expression .= ' ?';
            }
        }

        $whereCondition = $this->convertPlaceholders($expression, $value);

        return $whereCondition;
    }

    /** {@inheritdoc} */
    public function columns(array $columns = null)
    {
        $this->columns = $columns;

        return $this;
    }

    /** {@inheritdoc} */
    public function column($column, $args = [], $alias = '')
    {
        if ($this->columns === null) {
            $this->columns = [];
        }

        $expression = $this->convertPlaceholders($column, $args);

        $this->columns[] = $expression . ($alias ? ' AS ' . $alias : '');

        return $this;
    }

    /**
     * This function does nothing. We just overwrite the functionality from parenthesis.
     *
     * @return self
     */
    public function close()
    {
        return $this;
    }

    /**
     * Creates the $join statement.
     *
     * @param string $join
     * @param string $tableName
     * @param string $expression
     * @param string $alias
     * @param array|mixed $args
     * @param bool $empty
     * @return self|ParenthesisInterface
     */
    protected function createJoin($join, $tableName, $expression, $alias, $args, $empty)
    {
        $join = $join . ' ' . $tableName
              . ($alias ? ' AS ' . $alias : '');

        if (preg_match('/^[A-Za-z_]+$/', $expression)) {
            $join .= ' USING (' . $expression . ')';
            $this->joins[] = $join;
        } elseif ($expression) {
            $expression = $this->convertPlaceholders($expression, $args);

            $join .= ' ON ' . $expression;
            $this->joins[] = $join;
        } elseif ($empty) {
            $this->joins[] = $join;
        } else {
            return new Parenthesis(function (ParenthesisInterface $parenthesis) use ($join) {
                $join .= ' ON ' . $parenthesis->getParenthesis();
                $this->joins[] = $join;
                return $this;
            }, $this, $this->entityManager, $this->connection);
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function join($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin(
            'JOIN',
            $tableName,
            is_string($expression) ? $expression : '',
            $alias,
            $args,
            is_bool($expression) ? $expression : false
        );
    }

    /** {@inheritdoc} */
    public function leftJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin(
            'LEFT JOIN',
            $tableName,
            is_string($expression) ? $expression : '',
            $alias,
            $args,
            is_bool($expression) ? $expression : false
        );
    }

    /** {@inheritdoc} */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin(
            'RIGHT JOIN',
            $tableName,
            is_string($expression) ? $expression : '',
            $alias,
            $args,
            is_bool($expression) ? $expression : false
        );
    }

    /** {@inheritdoc} */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin(
            'FULL JOIN',
            $tableName,
            is_string($expression) ? $expression : '',
            $alias,
            $args,
            is_bool($expression) ? $expression : true
        );
    }

    /** {@inheritdoc} */
    public function groupBy($column, $args = [])
    {
        $this->groupBy[] = $this->convertPlaceholders($column, $args);

        return $this;
    }

    /** {@inheritdoc} */
    public function orderBy($column, $direction = self::DIRECTION_ASCENDING, $args = [])
    {
        $expression = $this->convertPlaceholders($column, $args);

        $this->orderBy[] = $expression . ' ' . $direction;

        return $this;
    }

    /** {@inheritdoc} */
    public function limit($limit)
    {
        $this->limit = (int)$limit;

        return $this;
    }

    /** {@inheritdoc} */
    public function offset($offset)
    {
        $this->offset = (int)$offset;

        return $this;
    }

    /** {@inheritdoc} */
    public function getQuery()
    {
        return 'SELECT '
               . (!empty($this->modifier) ? implode(' ', $this->modifier) . ' ' : '')
               . ($this->columns ? implode(',', $this->columns) : '*')
               . ' FROM ' . $this->tableName . ($this->alias ? ' AS ' . $this->alias : '')
               . (!empty($this->joins) ? ' ' . reset($this->joins) : '')
               . (!empty($this->where) ? ' WHERE ' . implode(' ', $this->where) : '')
               . (!empty($this->groupBy) ? ' GROUP BY ' . implode(',', $this->groupBy) : '')
               . (!empty($this->orderBy) ? ' ORDER BY ' . implode(',', $this->orderBy) : '')
               . ($this->limit ? ' LIMIT ' . $this->limit . ($this->offset ? ' OFFSET ' . $this->offset : '') : '');
    }

    /** {@inheritdoc} */
    public function modifier($modifier)
    {
        $this->modifier[] = $modifier;

        return $this;
    }
}
