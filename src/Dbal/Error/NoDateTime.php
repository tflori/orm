<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class NoDateTime extends Error
{
    const ERROR_CODE = 'NO_DATETIME';

    /** @var string */
    protected $message = '%value% is not a valid date or date time expression';
}
