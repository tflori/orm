<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Column;
use ORM\Dbal\Error;

/**
 * NotValid Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NotValid extends Error
{
    const ERROR_CODE = 'NOT_VALID';

    /** @var string */
    protected $message = 'Value not valid for %column% (Caused by: %previous%)';

    /**
     * NotValid constructor
     *
     * @param Column $column   The column that got a not valid error
     * @param Error  $previous The error from validate
     */
    public function __construct(Column $column, Error $previous)
    {
        parent::__construct([
            'column' => $column->name,
            'previous' => $previous->getMessage()
        ], null, null, $previous);
    }
}
