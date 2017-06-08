<?php

namespace ORM\Exception;

use ORM\Dbal\Error;
use ORM\Exception;

class NotValid extends Exception
{
    public $error;

    public function __construct(Error $error)
    {
        $this->error = $error;
        parent::__construct($error->getMessage());
    }
}
