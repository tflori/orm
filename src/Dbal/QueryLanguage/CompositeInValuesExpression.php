<?php

namespace ORM\Dbal\QueryLanguage;

trait CompositeInValuesExpression
{
    use CompositeInExpression;

    public function buildCompositeInExpression(array $cols, array $values, $inverse = false)
    {
        return '(' . implode(',', $cols) . ') ' . ($inverse ? 'NOT IN' : 'IN')  .
            ' (VALUES ' . implode(',', $this->buildTuples($values)) . ')';
    }
}
