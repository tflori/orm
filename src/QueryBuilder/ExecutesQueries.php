<?php

namespace ORM\QueryBuilder;

use PDOStatement;

trait ExecutesQueries
{
    /** The result object from PDO
     * @var PDOStatement */
    protected $result;

    /** The rows returned
     * @var array */
    protected $rows = [];

    /** The position of the cursor
     * @var int  */
    protected $cursor = 0;

    /**
     * Proxy to PDOStatement::setFetchMode()
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * @param int $mode
     * @param null $classNameObject
     * @param array $ctorarfg
     * @return $this
     * @see PDOStatement::setFetchMode()
     */
    public function setFetchMode($mode, $classNameObject = null, array $ctorarfg = [])
    {
        $result = $this->getStatement();
        if (!$result) {
            return $this;
        }

        $result->setFetchMode($mode, $classNameObject, $ctorarfg);
        return $this;
    }

    /**
     * Get the next row from the query result
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * If the query fails you should get an exception. Anyway if we couldn't get a result or there are no more rows
     * it returns null.
     *
     * @return mixed|null
     */
    public function one()
    {
        $result = $this->getStatement();
        if (!$result) {
            return null;
        }

        $cursor = $this->cursor;
        if (!isset($this->rows[$cursor])) {
            $this->rows[$cursor] = $result->fetch();
        }

        $this->cursor++;
        return $this->rows[$cursor] ?: null;
    }

    /**
     * Get all rows from the query result
     *
     * Please note that this will execute the query - further modifications will not have any effect.
     *
     * If the query fails you should get an exception. Anyway if we couldn't get a result or there are no rows
     * it returns an empty array.
     *
     * @return mixed|null
     */
    public function all()
    {
        $result = [];
        while ($next = $this->one()) {
            $result[] = $next;
        }

        return $result;
    }

    /**
     * Reset the position of the cursor to the first row
     *
     * @return $this
     */
    public function reset()
    {
        $this->cursor = 0;

        return $this;
    }

    /**
     * Query database and return result
     *
     * Queries the database with current query and returns the resulted PDOStatement.
     *
     * If query failed it returns false. It also stores this failed result and to change the query afterwards will not
     * change the result.
     *
     * @return PDOStatement|bool
     */
    protected function getStatement()
    {
        if ($this->result === null) {
            $this->result = $this->entityManager->getConnection()->query($this->getQuery()) ?: false;
        }
        return $this->result;
    }
}
