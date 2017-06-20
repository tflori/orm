<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * TooLong Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class TooLong extends Error
{
    const ERROR_CODE = 'TOO_LONG';

    /** @var string */
    protected $message = '\'%value%\' is too long (max: %max%)';
}
