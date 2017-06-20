<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * NotAllowed Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NotAllowed extends Error
{
    const ERROR_CODE = 'NOT_ALLOWED';

    /** @var string */
    protected $message = '\'%value%\' is not allowed by this %type%';
}
