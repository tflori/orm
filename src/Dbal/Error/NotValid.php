<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Column;
use ORM\Dbal\Error;

class NotValid extends Error
{
    const ERROR_CODE = 'NOT_VALID';

    /** @var string */
    protected $message = 'Value not valid for %column% (Caused by: %previous%)';

    /** @var Error */
    protected $previous;

    /**
     * NotValid constructor
     *
     * @param Column $column The column that got a not valid error
     * @param Error  $previous The error from validate
     */
    public function __construct(Column $column, Error $previous)
    {
        parent::__construct();

        $this->previous = $previous;
        $this->params['column'] = $column->name;
        $this->params['previous'] = $previous->getMessage();
    }

    /**
     * Get the error that caused this error
     *
     * @return Error
     */
    public function getPrevious()
    {
        return $this->previous;
    }
}
