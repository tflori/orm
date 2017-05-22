<?php

namespace ORM\Dbal;

use ORM\Dbal\Column;
use ORM\EntityManager;
use ORM\Exception;
use ORM\Dbal\Validator\Error;

class Validator
{
    /** The columns from this table
     * @var Column[] */
    protected $columns;

    /**
     * Validator constructor.
     *
     * @param Column[] $columns
     */
    public function __construct(array $columns)
    {
        foreach ($columns as $column) {
            $this->columns[$column->getName()] = $column;
        }
    }

    /**
     * Validate $value for column $col.
     *
     * Returns an array with at least
     *
     * @param string $col
     * @param mixed $value
     * @return bool|Error
     * @throws Exception
     */
    public function validate($col, $value)
    {
        if (!($column = $this->getColumn($col))) {
            throw new Exception('Unknown column ' . $col);
        }

        if ($value === null) {
            if ($column->isNullable() || $column->hasDefault()) {
                return true;
            }

            return new Error\NotNullable($column);
        }

        if ($column->getType()->validate($value)) {
            return true;
        }

        return new Error\NotValid($column);
    }

    /**
     * Get the Column object for $col
     *
     * @param string $col
     * @return Column
     */
    protected function getColumn($col)
    {
        return isset($this->columns[$col]) ? $this->columns[$col] : null;
    }
}
