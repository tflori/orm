<?php

namespace ORM\Dbal\QueryLanguage;

trait WhereClause
{
    protected function buildWhereClause(array $where)
    {
        $whereClause = !empty($where) ? ' WHERE ' : '';
        $i = 0;
        foreach ($where as $column => $condition) {
            if ($i > 0 && (!is_numeric($column) || !preg_match('/^\s*(AND|OR)/i', $condition))) {
                $whereClause .= ' AND ';
            }
            $whereClause .= !is_numeric($column) ?
                $this->escapeIdentifier($column) . ' = ' . $this->escapeValue($condition) : 
                ' ' . $condition;
            $i++;
        }
        return $whereClause;
    }
}
