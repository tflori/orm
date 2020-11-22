<?php

namespace ORM\Dbal\QueryLanguage;

use ORM\Exception;

trait UpdateFromStatement
{
    use UpdateStatement;

    protected function buildUpdateFromStatement($table, array $where, array $updates, array $joins)
    {
        if (!empty($joins)) {
            list($fromTable, $condition) = $this->convertJoin(array_shift($joins));
            $fromClause = ' FROM ' . $fromTable .
                (!empty($joins) ? ' ' . implode(' ', $joins) : '');
            array_unshift($where, $condition);
        }

        return 'UPDATE ' . $this->escapeIdentifier($table) .
            $this->buildSetClause($updates) .
            $fromClause .
            $this->buildWhereClause($where);
    }

    protected function convertJoin($join)
    {
        if (!preg_match('/^JOIN\s+([^\s]+)\s+ON\s+(.*)/ism', $join, $match)) {
            throw new Exception\InvalidArgument(
                'Only inner joins with on clause are allowed in update from statements'
            );
        }
        $table = $match[1];
        $condition = $match[2];

        return [$table, $condition];
    }
}
