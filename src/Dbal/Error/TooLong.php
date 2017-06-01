<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class TooLong extends Error
{
    const ERROR_CODE = 'TOO_LONG';

    /** @var string */
    protected $message = '%value% is too long (max: %max%)';
}
