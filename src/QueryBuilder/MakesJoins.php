<?php

namespace ORM\QueryBuilder;

trait MakesJoins
{
    /** Joins get concatenated with space
     * @var string[] */
    protected $joins = [];

    /**
     * Common implementation for *Join methods
     *
     * @param string $joinClause The beginning of the join clause (e. g. LEFT JOIN table AS x)
     * @param string|bool $expression Expression, single column name or boolean to create an empty join
     * @param array $args Arguments for expression
     * @return ParenthesisInterface|QueryBuilder
     * @internal
     */
    protected function createJoin($joinClause, $expression = '', $args = [])
    {
        /** @var QueryBuilder $this */
        $empty = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';

        if (preg_match('/^[A-Za-z_]+$/', $expression)) {
            $joinClause          .= ' USING (' . $expression . ')';
            $this->joins[] = $joinClause;
        } elseif ($expression) {
            $expression = $this->convertPlaceholders($expression, $args);

            $joinClause          .= ' ON ' . $expression;
            $this->joins[] = $joinClause;
        } elseif ($empty) {
            $this->joins[] = $joinClause;
        } else {
            return new Parenthesis(
                function (ParenthesisInterface $parenthesis) use ($joinClause) {
                    $joinClause          .= ' ON ' . $parenthesis->getExpression();
                    $this->joins[] = $joinClause;
                    return $this;
                },
                $this
            );
        }

        return $this;
    }

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
     * @return $this|ParenthesisInterface
     */
    public function join($tableName, $expression = '', $alias = '', $args = [])
    {
        $joinClause = 'JOIN ' . $tableName;
        $joinClause .= $alias ? ' AS ' . $alias : '';
        return $this->createJoin($joinClause, $expression, $args);
    }

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
     * @return $this|ParenthesisInterface
     */
    public function leftJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $joinClause = 'LEFT JOIN ' . $tableName;
        $joinClause .= $alias ? ' AS ' . $alias : '';
        return $this->createJoin($joinClause, $expression, $args);
    }

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
     * @return $this|ParenthesisInterface
     */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $joinClause = 'RIGHT JOIN ' . $tableName;
        $joinClause .= $alias ? ' AS ' . $alias : '';
        return $this->createJoin($joinClause, $expression, $args);
    }

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
     * @return $this|ParenthesisInterface
     */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        $joinClause = 'FULL JOIN ' . $tableName;
        $joinClause .= $alias ? ' AS ' . $alias : '';
//        $expression = $expression === '' ? true : $expression;
        return $this->createJoin($joinClause, $expression, $args);
    }
}
