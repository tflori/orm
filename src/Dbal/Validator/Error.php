<?php

namespace ORM\Dbal\Validator;

use ORM\Dbal\Column;

abstract class Error
{
    /** @var Column */
    public $column;

    /**
     * Error constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }
}
