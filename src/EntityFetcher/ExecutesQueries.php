<?php

namespace ORM\EntityFetcher;

trait ExecutesQueries
{
    /**
     * Execute an update statement for the current query
     *
     * **NOTE:** not all drivers support UPDATE with JOIN (or FROM). Has to be implemented in the database abstraction
     * layer.
     *
     * $updates should be an array which columns to update with what value. Use expressions to bypass escaping.
     *
     * @param array $updates An array of columns to update
     * @return int The number of affected rows
     */
    public function update(array $updates)
    {
        $updates = array_combine(
            array_map([$this->class, 'getColumnName'], array_keys($updates)),
            array_values($updates)
        );
        return parent::update($updates);
    }

    /**
     * Execute an insert statement on the current table
     *
     * @param array ...$rows
     * @return int The number of inserted rows
     */
    public function insert(array ...$rows)
    {
        $rows = array_map(function ($row) {
            return array_combine(
                array_map([$this->class, 'getColumnName'], array_keys($row)),
                array_values($row)
            );
        }, $rows);
        return parent::insert(...$rows);
    }
}
