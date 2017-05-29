<?php

namespace ORM\Dbal\Error;

use ORM\Dbal\Error;

class NotNullable extends Error
{
    protected $message = '%column% does not allow null values';
}
