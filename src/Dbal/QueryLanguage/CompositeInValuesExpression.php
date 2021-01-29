<?php

namespace ORM\Dbal\QueryLanguage;

trait CompositeInValuesExpression
{
    use CompositeInExpression;

    /**
     * Build a where in statement for composite keys
     *
     * @param array $cols Columns from which to build the composite key
     * @param array[] $values Array of composite keys (array)
     * @param bool $inverse Whether it should be a IN or NOT IN operator
     * @return string
     * @internal
     */
    public function buildCompositeInExpression(array $cols, array $values, $inverse = false)
    {
        return '(' . implode(',', $cols) . ') ' . ($inverse ? 'NOT IN' : 'IN')  .
            ' (VALUES ' . implode(',', $this->buildTuples($values)) . ')';
    }
}
