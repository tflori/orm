<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class NoNumber extends Error
{
    const ERROR_CODE = 'NO_NUMBER';

    /** @var string */
    protected $message = '%value% is not numeric';
}
