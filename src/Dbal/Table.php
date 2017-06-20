<?php

namespace ORM\Dbal;

use ArrayObject;
use ORM\Exception\UnknownColumn;

/**
 * Table is basically an array of Columns
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 * @method Column offsetGet($offset)
 */
class Table extends ArrayObject
{
    /** The columns from this table
     * @var Column[] */
    protected $columns;

    /**
     * Table constructor.
     *
     * @param Column[] $columns
     */
    public function __construct(array $columns)
    {
        foreach ($columns as $column) {
            $this->columns[$column->name] = $column;
        }

        parent::__construct($columns);
    }

    /**
     * Validate $value for column $col.
     *
     * Returns an array with at least
     *
     * @param string $col
     * @param mixed  $value
     * @return bool|Error
     * @throws UnknownColumn
     */
    public function validate($col, $value)
    {
        if (!($column = $this->getColumn($col))) {
            throw new UnknownColumn('Unknown column ' . $col);
        }

        return $column->validate($value);
    }

    /**
     * Get the Column object for $col
     *
     * @param string $col
     * @return Column
     */
    public function getColumn($col)
    {
        return isset($this->columns[$col]) ? $this->columns[$col] : null;
    }
}
