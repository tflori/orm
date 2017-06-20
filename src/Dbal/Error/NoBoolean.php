<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * NoBoolean Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NoBoolean extends Error
{
    const ERROR_CODE = 'NO_BOOLEAN';

    /** @var string */
    protected $message = '%value% can not be converted to boolean';
}
