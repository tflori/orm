<?php

namespace ORM\Dbal\QueryLanguage;

trait WhereClause
{
    protected function buildWhereClause(array $whereConditions)
    {
        if (empty($whereConditions)) {
            return '';
        }

        $normalized = [];
        foreach ($whereConditions as $column => $condition) {
            if (!is_numeric($column)) {
                $condition = $this->escapeIdentifier($column) . ' = ' . $this->escapeValue($condition);
            }

            if (!empty($normalized) && !preg_match('/^\s*(AND|OR)/i', $condition)) {
                $condition = 'AND ' . $condition;
            }

            $normalized[] = trim($condition);
        }

        return ' WHERE ' . implode(' ', $normalized);
    }
}
