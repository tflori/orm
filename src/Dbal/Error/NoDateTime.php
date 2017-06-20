<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * NoDateTime Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NoDateTime extends Error
{
    const ERROR_CODE = 'NO_DATETIME';

    /** @var string */
    protected $message = '%value% is not a valid date or date time expression';
}
