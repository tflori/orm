<?php

namespace ORM;

use ORM\QueryBuilder\ParenthesisInterface;

/**
 * Interface QueryBuilderInterface
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
interface QueryBuilderInterface extends ParenthesisInterface
{
    const DIRECTION_ASCENDING = 'ASC';
    const DIRECTION_DESCENDING = 'DESC';

    /**
     * (Inner) join $tableName with $options
     *
     * When no expression got provided a ParenthesisInterface get returned. If this parenthesis not get filled you
     * will most likely get an error from your database. If you don't want to get a parenthesis the parameter empty
     * can be set to true.
     *
     * @param string $tableName
     * @param string|boolean $expression|$empty
     * @param string $alias
     * @param array $args
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
     * @param string $tableName
     * @param string|boolean $expression|$empty
     * @param string $alias
     * @param array $args
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
     * @param string $tableName
     * @param string|boolean $expression|$empty
     * @param string $alias
     * @param array $args
     * @return self|ParenthesisInterface
     */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Right (outer) join $tableName with $options
     *
     * When no expression got provided self get returned. If you want to get a parenthesis the parameter empty
     * can be set to false.
     *
     * ATTENTION: here the default value of empty got changed - defaults to yes
     *
     * @param string $tableName
     * @param string|boolean $expression|$empty
     * @param string $alias
     * @param array $args
     * @return self|ParenthesisInterface
     */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = []);

    /**
     * Group By $column
     *
     * Optionally you can provide an expression in $column with question marks as placeholders.
     *
     * @param string $column
     * @param array $args
     * @return self
     */
    public function groupBy($column, $args = []);

    /**
     * Order By $column in $direction
     *
     * Optionally you can provide an expression in $column with question marks as placeholders.
     *
     * @param string $column
     * @param string $direction
     * @param array $args
     * @return self
     */
    public function orderBy($column, $direction = self::DIRECTION_ASCENDING, $args = []);

    /**
     * Set $limit
     *
     * @param int $limit
     * @return self
     */
    public function limit($limit);

    /**
     * Set $offset
     *
     * @param int $offset
     * @return self
     */
    public function offset($offset);

    /**
     * @return string
     */
    public function getQuery();
}
