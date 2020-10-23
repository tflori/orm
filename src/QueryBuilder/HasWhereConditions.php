<?php

namespace ORM\QueryBuilder;

trait HasWhereConditions
{
    /** Where conditions get concatenated with space
     * @var string[] */
    protected $where = [];

    /**
     * {@inheritdoc}
     * @internal
     */
    public function createWhereCondition($column, $operator = null, $value = null)
    {
        return $this->parent->createWhereCondition($column, $operator, $value);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function buildWhereInExpression($column, array $values, $inverse = false)
    {
        return $this->parent->buildWhereInExpression($column, $values, $inverse);
    }

    /** {@inheritdoc} */
    public function where($column, $operator = null, $value = null)
    {
        return $this->andWhere($column, $operator, $value);
    }

    /** {@inheritdoc} */
    public function andWhere($column, $operator = null, $value = null)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->createWhereCondition($column, $operator, $value);
        return $this;
    }

    /** {@inheritdoc} */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->createWhereCondition($column, $operator, $value);
        return $this;
    }

    /** {@inheritdoc} */
    public function whereIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->buildWhereInExpression($column, $values);
        return $this;
    }

    /** {@inheritdoc} */
    public function orWhereIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->buildWhereInExpression($column, $values);
        return $this;
    }

    /** {@inheritdoc} */
    public function whereNotIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('AND') . $this->buildWhereInExpression($column, $values, true);
        return $this;
    }

    /** {@inheritdoc} */
    public function orWhereNotIn($column, array $values)
    {
        $this->where[] = $this->wherePrefix('OR') . $this->buildWhereInExpression($column, $values, true);
        return $this;
    }

    /**
     * Get the prefix for a where condition or empty if not needed
     *
     * @param $bool
     * @return string
     */
    private function wherePrefix($bool)
    {
        return !empty($this->where) ? $bool . ' ' : '';
    }
}
