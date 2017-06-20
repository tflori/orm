<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Error\NoNumber;
use ORM\Dbal\Type;

/**
 * Float, double and decimal data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Number extends Type
{
    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_int($value) && !is_double($value) && (!is_string($value) || !is_numeric($value))) {
            return new NoNumber([ 'value' => (string) $value ]);
        }

        return true;
    }
}
