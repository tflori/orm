<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Column;
use ORM\Dbal\Error;

/**
 * NotNullable Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NotNullable extends Error
{
    const ERROR_CODE = 'NOT_NULLABLE';

    protected $message = '%column% does not allow null values';

    public function __construct(Column $column)
    {
        parent::__construct([ 'column' => $column->name ]);
    }
}
