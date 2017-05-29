<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Column;
use ORM\Dbal\Error;

class NotValid extends Error
{
    const ERROR_CODE = 'NOT_VALID';

    /** @var string */
    protected $code = self::ERROR_CODE;

    /** @var string */
    protected $message = 'Value not valid for this column';
}
