<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class InvalidJson extends Error
{
    const ERROR_CODE = 'INVALID_JSON';

    /** @var string */
    protected $message = '\'%value%\' is not a valid JSON string';
}
