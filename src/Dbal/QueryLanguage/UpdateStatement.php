<?php

namespace ORM\Dbal\QueryLanguage;

trait UpdateStatement
{
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

    protected function buildWhereClause(array $where)
    {
        $whereClause = !empty($where) ? ' WHERE ' : '';
        $i = 0;
        foreach ($where as $column => $condition) {
            if ($i > 0 && (!is_numeric($column) || !preg_match('/^\s*(AND|OR)/i', $condition))) {
                $whereClause .= ' AND ';
            }
            $whereClause .= !is_numeric($column) ?
                $this->escapeIdentifier($column) . ' = ' . $this->escapeValue($condition) : $condition;
            $i++;
        }
        return $whereClause;
    }
}
