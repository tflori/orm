<?php

namespace ORM\QueryBuilder;

use ORM\EntityManager;
use ORM\Exception\NoOperator;

/**
 * Build a ansi sql query / select statement
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
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class QueryBuilder extends Parenthesis implements QueryBuilderInterface
{
    /** The table to query
     * @var string */
    protected $tableName = '';

    /** The alias of the main table
     * @var string */
    protected $alias = '';

    /** Columns to fetch (null is equal to ['*'])
     * @var array|null */
    protected $columns = null;

    /** Joins get concatenated with space
     * @var string[] */
    protected $joins = [];

    /** Limit amount of rows
     * @var int */
    protected $limit;

    /** Offset to start from
     * @var int */
    protected $offset;

    /** Group by conditions get concatenated with comma
     * @var string[] */
    protected $groupBy = [];

    /** Order by conditions get concatenated with comma
     * @var string[] */
    protected $orderBy = [];

    /** Modifiers get concatenated with space
     * @var string[] */
    protected $modifier = [];

    /** EntityManager to use for quoting
     * @var EntityManager */
    protected $entityManager;

    /** The default EntityManager to use to for quoting
     * @var EntityManager */
    public static $defaultEntityManager;

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * Constructor
     *
     * Create a select statement for $tableName with an object oriented interface.
     *
     * It uses static::$defaultEntityManager if $entityManager is not given.
     *
     * @param string        $tableName     The main table to use in FROM clause
     * @param string        $alias         An alias for the table
     * @param EntityManager $entityManager EntityManager for quoting
     */
    public function __construct($tableName, $alias = '', EntityManager $entityManager = null)
    {
        $this->tableName     = $tableName;
        $this->alias         = $alias;
        $this->entityManager = $entityManager;
    }

    /**
     * Replaces question marks in $expression with $args
     *
     * @param string      $expression Expression with placeholders
     * @param array|mixed $args       Arguments for placeholders
     * @return string
     * @throws \ORM\Exception\NoConnection
     * @throws \ORM\Exception\NotScalar
     */
    protected function convertPlaceholders(
        $expression,
        $args
    ) {
        if (strpos($expression, '?') === false) {
            return $expression;
        }

        if (!is_array($args)) {
            $args = [ $args ];
        }

        $parts      = explode('?', $expression);
        $expression = '';
        while ($part = array_shift($parts)) {
            $expression .= $part;
            if (count($args)) {
                $expression .= $this->getEntityManager()->escapeValue(array_shift($args));
            } elseif (count($parts)) {
                $expression .= '?';
            }
        }

        return $expression;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager ?: static::$defaultEntityManager;
    }

    /**
     * Common implementation for creating a where condition
     *
     * @param string $column   Column or expression with placeholders
     * @param string $operator Operator or value if operator is omited
     * @param string $value    Value or array of values
     * @return string
     * @throws NoOperator
     * @internal
     */
    public function createWhereCondition($column, $operator = null, $value = null)
    {
        if (strpos($column, '?') !== false) {
            $expression = $column;
            $value      = $operator;
        } elseif ($operator === null && $value === null) {
            $expression = $column;
        } else {
            if ($value === null) {
                $value    = $operator;
                $operator = null;
            }

            $expression = $this->buildExpression($column, $value, $operator);
        }

        $whereCondition = $this->convertPlaceholders($expression, $value);

        return $whereCondition;
    }

    private function buildExpression($column, $value, $operator = null)
    {
        $operator   = $operator ?: $this->getDefaultOperator($value);
        $expression = $column . ' ' . $operator;

        if (in_array(strtoupper($operator), [ 'IN', 'NOT IN' ]) && is_array($value)) {
            $expression .= ' (?' . str_repeat(',?', count($value) - 1) . ')';
        } else {
            $expression .= ' ?';
        }

        return $expression;
    }

    private function getDefaultOperator($value)
    {
        if (is_array($value)) {
            return 'IN';
        } else {
            return '=';
        }
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

    /** @internal
     * @return self */
    public function close()
    {
        return $this;
    }

    /**
     * Common implementation for *Join methods
     *
     * @param string      $join       The join type (e. g. `LEFT JOIN`)
     * @param string      $tableName  Table name to join
     * @param string      $expression Expression to use in on clause or single column for USING
     * @param string      $alias      Alias for the table
     * @param array|mixed $args       Arguments to use in $expression
     * @param bool        $empty      Create an empty join (without USING and ON)
     * @return ParenthesisInterface|QueryBuilder
     * @throws \ORM\Exception\NoConnection
     * @throws \ORM\Exception\NotScalar
     * @internal
     */
    protected function createJoin($join, $tableName, $expression, $alias, $args, $empty)
    {
        $join = $join . ' ' . $tableName
                . ($alias ? ' AS ' . $alias : '');

        if (preg_match('/^[A-Za-z_]+$/', $expression)) {
            $join          .= ' USING (' . $expression . ')';
            $this->joins[] = $join;
        } elseif ($expression) {
            $expression = $this->convertPlaceholders($expression, $args);

            $join          .= ' ON ' . $expression;
            $this->joins[] = $join;
        } elseif ($empty) {
            $this->joins[] = $join;
        } else {
            return new Parenthesis(
                function (ParenthesisInterface $parenthesis) use ($join) {
                    $join          .= ' ON ' . $parenthesis->getExpression();
                    $this->joins[] = $join;
                    return $this;
                },
                $this
            );
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function join($tableName, $expression = '', $alias = '', $args = [])
    {
        $empty      = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';
        return $this->createJoin('JOIN', $tableName, $expression, $alias, $args, $empty);
    }

    /** {@inheritdoc} */
    public function leftJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $empty      = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';
        return $this->createJoin('LEFT JOIN', $tableName, $expression, $alias, $args, $empty);
    }

    /** {@inheritdoc} */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $empty      = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';
        return $this->createJoin('RIGHT JOIN', $tableName, $expression, $alias, $args, $empty);
    }

    /** {@inheritdoc} */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $empty      = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';
        return $this->createJoin('FULL JOIN', $tableName, $expression, $alias, $args, $empty);
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
        $this->limit = (int) $limit;

        return $this;
    }

    /** {@inheritdoc} */
    public function offset($offset)
    {
        $this->offset = (int) $offset;

        return $this;
    }

    /** {@inheritdoc} */
    public function getQuery()
    {
        return 'SELECT '
               . (!empty($this->modifier) ? implode(' ', $this->modifier) . ' ' : '')
               . ($this->columns ? implode(',', $this->columns) : '*')
               . ' FROM ' . $this->tableName . ($this->alias ? ' AS ' . $this->alias : '')
               . (!empty($this->joins) ? ' ' . implode(' ', $this->joins) : '')
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
