<?php

namespace ORM\Dbal\QueryLanguage;

trait InsertStatement
{
    /**
     * Build an insert statement for $rows
     *
     * @param string $table
     * @param array $rows
     * @return string
     */
    protected function buildInsert($table, array $rows)
    {
        // get all columns from rows
        $columns = [];
        foreach ($rows as $row) {
            $columns = array_unique(array_merge($columns, array_keys($row)));
        }

        $statement = 'INSERT INTO ' . $this->escapeIdentifier($table) . ' ' .
            '(' . implode(',', array_map([$this, 'escapeIdentifier'], $columns)) . ') VALUES ';

        $statement .= implode(',', array_map(function ($values) use ($columns) {
            return '(' . implode(',', array_map(function ($column) use ($values) {
                return isset($values[$column]) ? $this->escapeValue($values[$column]) : $this->escapeNULL();
            }, $columns)) . ')';
        }, $rows));

        return $statement;
    }
}
