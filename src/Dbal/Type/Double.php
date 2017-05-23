<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

/**
 * Float, double and decimal data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Double extends Type
{
    public function validate($value)
    {
        if (is_int($value) || is_double($value)) {
            return true;
        }

        if (is_string($value) && is_numeric($value)) {
            return true;
        }

        return false;
    }
}
