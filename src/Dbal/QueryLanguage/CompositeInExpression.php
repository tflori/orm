<?php

namespace ORM\Dbal\QueryLanguage;

trait CompositeInExpression
{
    /**
     * Build a where in expression for composite values
     *
     * @param array $cols
     * @param array $values
     * @param bool $inverse Whether it should be a IN or NOT IN operator
     * @return string
     */
    public function buildCompositeInExpression(array $cols, array $values, $inverse = false)
    {
        return '(' . implode(',', $cols) . ') ' . ($inverse ? 'NOT IN' : 'IN')  .
            ' (' . implode(',', $this->buildTuples($values)) . ')';
    }

    protected function buildTuples(array $values)
    {
        return array_map(function ($value) {
            return '(' . implode(',', array_map([$this, 'escapeValue'], $value)) . ')';
        }, $values);
    }
}
