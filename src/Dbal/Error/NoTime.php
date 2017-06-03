<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class NoTime extends Error
{
    const ERROR_CODE = 'NO_TIME';

    /** @var string */
    protected $message = '%value% is not a valid time expression';
}
