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
     * ```php?start_inline=true
     * where('name', '=' , 'John Doe')
     * where('name = ?', 'John Doe')
     * where('name', 'John Doe')
     * where('name = ?', ['John Doe'])
     * ```
     *
     * @see ParenthesisInterface::andWhere()
     * @param string       $column   Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string       $value    Value (required when used with operator)
     * @return self
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
     * ```php?start_inline=true
     * andWhere('name', '=' , 'John Doe')
     * andWhere('name = ?', 'John Doe')
     * andWhere('name', 'John Doe')
     * andWhere('name = ?', ['John Doe'])
     * ```
     *
     * @param string       $column   Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string       $value    Value (required when used with operator)
     * @return self
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
     * ```php?start_inline=true
     * orWhere('name', '=' , 'John Doe')
     * orWhere('name = ?', 'John Doe')
     * orWhere('name', 'John Doe')
     * orWhere('name = ?', ['John Doe'])
     * ```
     *
     * @param string       $column   Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string       $value    Value (required when used with operator)
     * @return self
     */
    public function orWhere($column, $operator = '', $value = '');

    /**
     * Alias for andParenthesis
     *
     * @see ParenthesisInterface::andWhere()
     * @return ParenthesisInterface
     */
    public function parenthesis();

    /**
     * Add a parenthesis with AND
     *
     * @return ParenthesisInterface
     */
    public function andParenthesis();

    /**
     * Add a parenthesis with OR
     *
     * @return ParenthesisInterface
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
}
