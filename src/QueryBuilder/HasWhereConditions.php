<?php

namespace ORM\QueryBuilder;

trait HasWhereConditions
{
    /** Where conditions get concatenated with space
     * @var string[] */
    protected $where = [];

    /**
     * Alias for andWhere
     *
     * QueryBuilderInterface where($column[, $operator[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * These calls are equal:
     *
     * ```php
     * $query->where('name', '=' , 'John Doe');
     * $query->where('name = ?', 'John Doe');
     * $query->where('name', 'John Doe');
     * $query->where('name = ?', ['John Doe']);
     * ```
     *
     * @see ParenthesisInterface::andWhere()
     * @param string $column   Column or expression with placeholders
     * @param mixed $operator Operator, value or array of values
     * @param mixed $value    Value (required when used with operator)
     * @return static
     */
    public function where($column, $operator = null, $value = null)
    {
        return $this->andWhere($column, $operator, $value);
    }

    /**
     * Add a where condition with AND.
     *
     * QueryBuilderInterface andWhere($column[, $operator[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * These calls are equal:
     *
     * ```php
     * $query->andWhere('name', '=' , 'John Doe');
     * $query->andWhere('name = ?', 'John Doe');
     * $query->andWhere('name', 'John Doe');
     * $query->andWhere('name = ?', ['John Doe']);
     * ```
     *
     * @param string       $column   Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string       $value    Value (required when used with operator)
     * @return static
     */
    public function andWhere($column, $operator = null, $value = null)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->createWhereCondition($column, $operator, $value);
        return $this;
    }

    /**
     * Add a where condition with OR.
     *
     * QueryBuilderInterface orWhere($column[, $operator[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * These calls are equal:
     *
     * ```php
     * $query->orWhere('name', '=' , 'John Doe');
     * $query->orWhere('name = ?', 'John Doe');
     * $query->orWhere('name', 'John Doe');
     * $query->orWhere('name = ?', ['John Doe']);
     * ```
     *
     * @param string       $column   Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string       $value    Value (required when used with operator)
     * @return static
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->createWhereCondition($column, $operator, $value);
        return $this;
    }

    /**
     * Add a where in condition with AND.
     *
     * If $column is an array a composite where in statement will be created
     *
     * Example:
     *  `whereIn(['a', 'b'], [[42, 23], [42, 23]])` gets `(a,b) IN ((42,23), (23,42))` in mysql
     *
     * If $values is empty the expression will be `1 = 0` because an empty parenthesis causes an error in SQL.
     *
     * @param string|array $column
     * @param array $values
     * @return static
     */
    public function whereIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->buildWhereInExpression($column, $values);
        return $this;
    }

    /**
     * Add a where in condition with OR.
     *
     * If $column is an array a composite where in statement will be created
     *
     * Example:
     *  `whereIn(['a', 'b'], [[42, 23], [42, 23]])` gets `(a,b) IN ((42,23), (23,42))` in mysql
     *
     * If $values is empty the expression will be `1 = 0` because an empty parenthesis causes an error in SQL.
     *
     * @param string|array $column
     * @param array $values
     * @return static
     */
    public function orWhereIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->buildWhereInExpression($column, $values);
        return $this;
    }

    /**
     * Add a where not in condition with AND.
     *
     * If $column is an array a composite where in statement will be created
     *
     * Example:
     *  `whereIn(['a', 'b'], [[42, 23], [42, 23]])` gets `(a,b) NOT IN ((42,23), (23,42))` in mysql
     *
     * If $values is empty the expression will be `1 = 1` because an empty parenthesis causes an error in SQL.
     *
     * @param string|array $column
     * @param array $values
     * @return static
     */
    public function whereNotIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->buildWhereInExpression($column, $values, true);
        return $this;
    }

    /**
     * Add a where not in condition with OR.
     *
     * If $column is an array a composite where in statement will be created
     *
     * Example:
     *  `whereIn(['a', 'b'], [[42, 23], [42, 23]])` gets `(a,b) NOT IN ((42,23), (23,42))` in mysql
     *
     * If $values is empty the expression will be `1 = 1` because an empty parenthesis causes an error in SQL.
     *
     * @param string|array $column
     * @param array $values
     * @return static
     */
    public function orWhereNotIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->buildWhereInExpression($column, $values, true);
        return $this;
    }

    /**
     * Get the prefix for a where condition or empty if not needed
     *
     * @param string $bool The prefix to use ('AND' or 'OR')
     * @return string
     */
    private function wherePrefix($bool)
    {
        return !empty($this->where) ? $bool . ' ' : '';
    }
}
