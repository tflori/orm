<?php

namespace ORM\Dbal\QueryLanguage;

trait DeleteStatement
{
    use WhereClause;

    protected function buildDeleteStatement($table, array $where)
    {
        return 'DELETE FROM ' . $this->escapeIdentifier($table) .
            $this->buildWhereClause($where);
    }
}
