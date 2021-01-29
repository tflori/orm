<?php

namespace ORM\Dbal\QueryLanguage;

trait UpdateJoinStatement
{
    use UpdateStatement;

    protected function buildUpdateJoinStatement($table, array $where, array $updates, array $joins)
    {
        return 'UPDATE ' . $this->escapeIdentifier($table) .
            (!empty($joins) ? ' ' . implode(' ', $joins) : '') .
            $this->buildSetClause($updates) .
            $this->buildWhereClause($where);
    }
}
