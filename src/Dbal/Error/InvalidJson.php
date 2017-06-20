<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * InvalidJson Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class InvalidJson extends Error
{
    const ERROR_CODE = 'INVALID_JSON';

    /** @var string */
    protected $message = '\'%value%\' is not a valid JSON string';
}
