<?php

namespace ORM;

use ORM\QueryBuilder\Parenthesis;
use ORM\QueryBuilder\ParenthesisInterface;

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
     * Set $columns
     *
     * @param $columns
     * @return self
     */
    public function columns(array $columns = null)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Add $column
     *
     * Optionally you can provide an expression with question marks as placeholders filled with $args.
     *
     * @param string $column
     * @param array $args
     * @return QueryBuilder
     */
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
     * Empty
     *
     * This function does nothing. We just overwrite the functionality from parenthesis.
     *
     * @return self
     */
    public function close()
    {
        return $this;
    }

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
            }, $this->entityManager, $this->connection);
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
        return 'SELECT ' . ($this->columns ? implode(',', $this->columns) : '*')
               . ' FROM ' . $this->tableName . ($this->alias ? ' AS ' . $this->alias : '')
               . (!empty($this->joins) ? ' ' . reset($this->joins) : '')
               . (!empty($this->where) ? ' WHERE ' . implode(' ', $this->where) : '')
               . (!empty($this->groupBy) ? ' GROUP BY ' . implode(',', $this->groupBy) : '')
               . (!empty($this->orderBy) ? ' ORDER BY ' . implode(',', $this->orderBy) : '')
               . ($this->limit ? ' LIMIT ' . $this->limit . ($this->offset ? ' OFFSET ' . $this->offset : '') : '');
    }
}
