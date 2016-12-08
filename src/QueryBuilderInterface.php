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
     * Join $tablename with optional $alias.
     *
     * If $column is not defined it returns a ParenthesisInterface whe you should define
     * the join condition.
     *
     * If there is no fifth operator then the default operator is '=' and $column is the fourth operator.
     *
     * Equal calls:
     * join('phones', '', 'contact_id', '=', 'contact.id')
     * join('phones', '', 'contact_id = contact.id')
     * join('phones', '', 'contact_id', 'contact.id')
     *
     * If the names are equal you can also omit the fourth parameter:
     * join('phones', '', 'contact_id')
     * join('phones', '', 'contact_id', '=', 'contact_id')
     *
     * @param $tableName
     * @param string $alias
     * @param string $column
     * @param string $operator
     * @param string $column
     * @return self|ParenthesisInterface
     */
    public function join($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function innerJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function leftJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function leftOuterJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function rightJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function rightOuterJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function fullJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');
    public function fullOuterJoin($tableName, $alias = '', $column = '', $operator = '', $column = '');

    /**
     * @param string $column
     * @param array $args
     * @return self
     */
    public function groupBy($column, $args = []);

    /**
     * @param string $column
     * @param string $direction
     * @param array $args
     * @return self
     */
    public function orderBy($column, $direction = self::DIRECTION_ASCENDING, $args = []);

    /**
     * @param int $limit
     * @return self
     */
    public function limit($limit);

    /**
     * @param int $offset
     * @return self
     */
    public function offset($offset);
}
