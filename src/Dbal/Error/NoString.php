<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

/**
 * NoString Validation Error
 *
 * @package ORM\Dbal\Error
 * @author  Thomas Flori <thflori@gmail.com>
 */
class NoString extends Error
{
    const ERROR_CODE = 'NO_STRING';

    /** @var string */
    protected $message = 'Only string values are allowed for %type%';
}
