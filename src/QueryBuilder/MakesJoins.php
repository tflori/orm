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
     * @param string $join The join type (e. g. `LEFT JOIN`)
     * @param string $tableName Table name to join
     * @param string|boolean $expression Expression, single column name or boolean to create an empty join
     * @param string $alias Alias for the table
     * @param array $args Arguments for expression
     * @return ParenthesisInterface|QueryBuilder
     * @internal
     */
    protected function createJoin($join, $tableName, $expression = '', $alias = '', $args = [])
    {
        $empty = is_bool($expression) ? $expression : false;
        $expression = is_string($expression) ? $expression : '';

        $join = $join . ' ' . $tableName;
        $join .= $alias ? ' AS ' . $alias : '';

        if (preg_match('/^[A-Za-z_]+$/', $expression)) {
            $join          .= ' USING (' . $expression . ')';
            $this->joins[] = $join;
        } elseif ($expression) {
            $expression = $this->convertPlaceholders($expression, $args);

            $join          .= ' ON ' . $expression;
            $this->joins[] = $join;
        } elseif ($empty) {
            $this->joins[] = $join;
        } else {
            return new Parenthesis(
                function (ParenthesisInterface $parenthesis) use ($join) {
                    $join          .= ' ON ' . $parenthesis->getExpression();
                    $this->joins[] = $join;
                    return $this;
                },
                $this
            );
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function join($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin('JOIN', $tableName, $expression, $alias, $args);
    }

    /** {@inheritdoc} */
    public function leftJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin('LEFT JOIN', $tableName, $expression, $alias, $args);
    }

    /** {@inheritdoc} */
    public function rightJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin('RIGHT JOIN', $tableName, $expression, $alias, $args);
    }

    /** {@inheritdoc} */
    public function fullJoin($tableName, $expression = '', $alias = '', $args = [])
    {
        return $this->createJoin('FULL JOIN', $tableName, $expression, $alias, $args);
    }
}
