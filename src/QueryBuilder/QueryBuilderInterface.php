<?php

namespace ORM\QueryBuilder;

/**
 * Interface QueryBuilderInterface
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
interface QueryBuilderInterface extends ParenthesisInterface
{
    const DIRECTION_ASCENDING  = 'ASC';
    const DIRECTION_DESCENDING = 'DESC';

    /**
     * Set $columns
     *
     * @param $columns
     * @return self
     */
    public function columns(array $columns = null);

    /**
     * Add $column
     *
     * Optionally you can provide an expression with question marks as placeholders filled with $args.
     *
     * @param string $column Column or expression to fetch
     * @param array  $args   Arguments for expression
     * @param string $alias  Alias for the column
     * @return QueryBuilder
     */
    public function column($column, $args = [], $alias = '');

    /**
     * (Inner) join $tableName with $options
     *
     * When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
     * will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
     * can be set to true.
     *
     * @param string         $tableName  Table to join
     * @param string|boolean $expression Expression, single column name or boolean to create an empty join
     * @param string         $alias      Alias for the table
     * @param array          $args       Arguments for expression
     * @return self|ParenthesisInterface
     */
    public function join($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Left (outer) join $tableName with $options
     *
     * When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
     * will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
     * can be set to true.
     *
     * @param string         $tableName  Table to join
     * @param string|boolean $expression Expression, single column name or boolean to create an empty join
     * @param string         $alias      Alias for the table
     * @param array          $args       Arguments for expression
     * @return self|ParenthesisInterface
     */
    public function leftJoin($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Right (outer) join $tableName with $options
     *
     * When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
     * will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
     * can be set to true.
     *
     * @param string         $tableName  Table to join
     * @param string|boolean $expression Expression, single column name or boolean to create an empty join
     * @param string         $alias      Alias for the table
     * @param array          $args       Arguments for expression
     * @return self|ParenthesisInterface
     */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Full (outer) join $tableName with $options
     *
     * When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
     * can be set to false.
     *
     * ATTENTION: here the default value of empty got changed - defaults to yes
     *
     * @param string         $tableName  Table to join
     * @param string|boolean $expression Expression, single column name or boolean to create an empty join
     * @param string         $alias      Alias for the table
     * @param array          $args       Arguments for expression
     * @return self|ParenthesisInterface
     */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Group By $column
     *
     * Optionally you can provide an expression in $column with question marks as placeholders.
     *
     * @param string $column Column or expression for groups
     * @param array  $args   Arguments for expression
     * @return self
     */
    public function groupBy($column, $args = []);

    /**
     * Order By $column in $direction
     *
     * Optionally you can provide an expression in $column with question marks as placeholders.
     *
     * @param string $column    Column or expression for order
     * @param string $direction Direction (default: `ASC`)
     * @param array  $args      Arguments for expression
     * @return self
     */
    public function orderBy($column, $direction = self::DIRECTION_ASCENDING, $args = []);

    /**
     * Set $limit
     *
     * Limits the amount of rows fetched from database.
     *
     * @param int $limit The limit to set
     * @return self
     */
    public function limit($limit);

    /**
     * Set $offset
     *
     * Changes the offset (only with limit) where fetching starts in the query.
     *
     * @param int $offset The offset to set
     * @return self
     */
    public function offset($offset);

    /**
     * Add $modifier
     *
     * Add query modifiers such as SQL_CALC_FOUND_ROWS or DISTINCT.
     *
     * @param string $modifier
     * @return self
     */
    public function modifier($modifier);

    /**
     * Get the query / select statement
     *
     * Builds the statement from current where conditions, joins, columns and so on.
     *
     * @return string
     */
    public function getQuery();
}
