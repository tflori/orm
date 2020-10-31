<?php

namespace ORM\QueryBuilder;

/**
 * Interface ParenthesisInterface
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
interface ParenthesisInterface
{
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
     * @return $this
     */
    public function where($column, $operator = '', $value = '');

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
     * @return $this
     */
    public function andWhere($column, $operator = '', $value = '');

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
     * @return $this
     */
    public function orWhere($column, $operator = '', $value = '');

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
     * @return $this
     */
    public function whereIn($column, array $values);

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
     * @return $this
     */
    public function orWhereIn($column, array $values);

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
     * @return $this
     */
    public function whereNotIn($column, array $values);

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
     * @return $this
     */
    public function orWhereNotIn($column, array $values);

    /**
     * Alias for andParenthesis
     *
     * @see ParenthesisInterface::andWhere()
     * @return $this
     */
    public function parenthesis();

    /**
     * Add a parenthesis with AND
     *
     * @return $this
     */
    public function andParenthesis();

    /**
     * Add a parenthesis with OR
     *
     * @return $this
     */
    public function orParenthesis();

    /**
     * Close parenthesis
     *
     * @return QueryBuilderInterface|ParenthesisInterface
     */
    public function close();

    /**
     * Get the expression
     *
     * Returns the complete expression inside this parenthesis.
     *
     * @return string
     */
    public function getExpression();

    /**
     * Create the where condition
     *
     * Calls createWhereCondition() from parent if there is a parent.
     *
     * @param string $column   Column or expression with placeholders
     * @param string $operator Operator, value or array of values
     * @param string $value    Value (required when used with operator)
     * @return string
     * @internal
     */
    public function createWhereCondition($column, $operator = null, $value = null);

    /**
     * Build a where in expression
     *
     * Calls buildWhereInExpression() from parent if there is a parent.
     *
     * @param string|array $column Column or expression with placeholders
     * @param array $values Value (required when used with operator)
     * @param bool $inverse
     * @return string
     * @internal
     */
    public function buildWhereInExpression($column, array $values, $inverse = false);
}
