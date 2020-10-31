<?php

namespace ORM\QueryBuilder;

use ORM\EntityManager;
use PDOStatement;

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
    use MakesJoins;

    /** The table to query
     * @var string */
    protected $tableName = '';

    /** The alias of the main table
     * @var string */
    protected $alias = '';

    /** Columns to fetch (null is equal to ['*'])
     * @var array|null */
    protected $columns = null;

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

    /** The result object from PDO
     * @var PDOStatement */
    protected $result;

    /** The rows returned
     * @var array */
    protected $rows = [];

    /** The position of the cursor
     * @var int  */
    protected $cursor = 0;

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
     * {@inheritdoc}
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
            $operator = $operator ?: $this->getDefaultOperator($value);

            if (in_array(strtoupper($operator), [ 'IN', 'NOT IN' ])) {
                return $this->buildWhereInExpression($column, $value, strtoupper($operator) === 'NOT IN');
            }

            $expression = $column . ' ' . $operator . ' ?';
        }

        return $this->convertPlaceholders($expression, $value);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function buildWhereInExpression($column, array $values, $inverse = false)
    {
        $em = $this->getEntityManager();
        if (empty($values)) {
            // nothing is in empty but everything is not in empty
            return $inverse ? '1 = 1' : '1 = 0';
        } elseif (is_array($column) && count($column) > 1) {
            return $em->getDbal()
                ->buildCompositeWhereInStatement($column, $values, $inverse);
        } else {
            if (is_array($column)) {
                $column = $this->first($column);
                $values = array_map([$this, 'first'], $values);
            }

            return vsprintf('%s %s %s', [
                $column,
                $inverse ? 'NOT IN' : 'IN',
                '(' . implode(',', array_map([$em, 'escapeValue'], $values)) . ')'
            ]);
        }
    }

    /**
     * Get the first item of an array
     *
     * Stupid helper for a missing functionality in php
     *
     * @param iterable $array
     * @return mixed|null
     * @codeCoverageIgnore trivial
     */
    private function first($array)
    {
        foreach ($array as $item) {
            return $item;
        }
        return null;
    }

    /**
     * Get the default operator for $value
     *
     * Arrays use `IN` by default - all others use `=`
     *
     * @param mixed $value The value to determine the operator
     * @return string
     */
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

    /**
     * @internal
     * @return $this
     */
    public function close()
    {
        return $this;
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

    /**
     * Proxy to PDOStatement::setFetchMode()
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * @param int $mode
     * @param null $classNameObject
     * @param array $ctorarfg
     * @return $this
     * @see PDOStatement::setFetchMode()
     */
    public function setFetchMode($mode, $classNameObject = null, array $ctorarfg = [])
    {
        $result = $this->getStatement();
        if (!$result) {
            return $this;
        }

        $result->setFetchMode($mode, $classNameObject, $ctorarfg);
        return $this;
    }

    /**
     * Get the next row from the query result
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * If the query fails you should get an exception. Anyway if we couldn't get a result or there are no more rows
     * it returns null.
     *
     * @return mixed|null
     */
    public function one()
    {
        $result = $this->getStatement();
        if (!$result) {
            return null;
        }

        $cursor = $this->cursor;
        if (!isset($this->rows[$cursor])) {
            $this->rows[$cursor] = $result->fetch();
        }

        $this->cursor++;
        return $this->rows[$cursor] ?: null;
    }

    /**
     * Get all rows from the query result
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * If the query fails you should get an exception. Anyway if we couldn't get a result or there are no rows
     * it returns an empty array.
     *
     * @return mixed|null
     */
    public function all()
    {
        $result = [];
        while ($next = $this->one()) {
            $result[] = $next;
        }

        return $result;
    }

    /**
     * Reset the position of the cursor to the first row
     *
     * @return $this
     */
    public function reset()
    {
        $this->cursor = 0;

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

    /**
     * Query database and return result
     *
     * Queries the database with current query and returns the resulted PDOStatement.
     *
     * If query failed it returns false. It also stores this failed result and to change the query afterwards will not
     * change the result.
     *
     * @return PDOStatement|bool
     */
    protected function getStatement()
    {
        if ($this->result === null) {
            $this->result = $this->entityManager->getConnection()->query($this->getQuery()) ?: false;
        }
        return $this->result;
    }

    /** {@inheritdoc} */
    public function modifier($modifier)
    {
        $this->modifier[] = $modifier;

        return $this;
    }
}
