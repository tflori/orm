<?php

namespace ORM\Dbal\QueryLanguage;

trait UpdateStatement
{
    use WhereClause;

    protected function buildUpdateStatement($table, array $where, array $updates)
    {
        return 'UPDATE ' . $this->escapeIdentifier($table) .
            $this->buildSetClause($updates) .
            $this->buildWhereClause($where);
    }

    protected function buildSetClause(array $updates)
    {
        return ' SET ' . implode(',', array_map(function ($column, $value) {
            return $this->escapeIdentifier($column) . ' = ' . $this->escapeValue($value);
        }, array_keys($updates), $updates));
    }
}
