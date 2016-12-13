<?php

namespace ORM\QueryBuilder;

use ORM\QueryBuilderInterface;

/**
 * Interface ParenthesisInterface
 *
 * @package ORM\QueryBuilder
 * @author Thomas Flori <thflori@gmail.com>
 */
interface ParenthesisInterface
{
    /**
     * Add a where condition with AND. Alias for andWhere.
     *
     * QueryBuilderInterface where($column|$expression[, $operator|$value[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * Equal calls:
     * <code>
     * where('name', '=' , 'John Doe')
     * where('name = ?', 'John Doe')
     * where('name', 'John Doe')
     * </code>
     *
     * @see ParenthesisInterface::andWhere()
     * @param string $column Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string $value Value (required if used with operator)
     * @return self
     */
    public function where($column, $operator = '', $value = '');

    /**
     * Add a where condition with AND.
     *
     * QueryBuilderInterface andWhere($column|$expression[, $operator|$value[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * Equal calls:
     * <code>
     * andWhere('name', '=' , 'John Doe')
     * andWhere('name = ?', 'John Doe')
     * andWhere('name', 'John Doe')
     * </code>
     *
     * @param string $column Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string $value Value (required if used with operator)
     * @return self
     */
    public function andWhere($column, $operator = '', $value = '');

    /**
     * Add a where condition with OR.
     *
     * QueryBuilderInterface orWhere($column|$expression[, $operator|$value[, $value]]);
     *
     * If $column has the same amount of question marks as $value - $value is the second parameter.
     *
     * If there is no third parameter and no question mark in $column then the default operator is '=' and $value is
     * the second parameter.
     *
     * Equal calls:
     * <code>
     * orWhere('name', '=' , 'John Doe')
     * orWhere('name = ?', 'John Doe')
     * orWhere('name', 'John Doe')
     * </code>
     *
     * @param string $column Column or expression with placeholders
     * @param string|array $operator Operator, value or array of values
     * @param string $value Value (required if used with operator)
     * @return self
     */
    public function orWhere($column, $operator = '', $value = '');

    /**
     * Add a parenthesis with AND. Alias for andParenthesis.
     *
     * @see ParenthesisInterface::andWhere()
     * @return ParenthesisInterface
     */
    public function parenthesis();

    /**
     * Add a parenthesis with AND.
     *
     * @return ParenthesisInterface
     */
    public function andParenthesis();

    /**
     * Add a parenthesis with OR.
     *
     * @return ParenthesisInterface
     */
    public function orParenthesis();

    /**
     * @return QueryBuilderInterface|ParenthesisInterface
     */
    public function close();

    /**
     * @return string
     */
    public function getParenthesis();

    /**
     * @param $column
     * @param string $operator
     * @param string $value
     * @internal
     * @return string
     */
    public function getWhereCondition($column, $operator = '', $value = '');
}
