<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * NoNumber Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NoNumber extends Error
{
    const ERROR_CODE = 'NO_NUMBER';

    /** @var string */
    protected $message = '%value% is not numeric';
}
