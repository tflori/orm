<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class NoString extends Error
{
    const ERROR_CODE = 'NO_STRING';

    /** @var string */
    protected $message = 'Only string values are allowed for %type%';
}
