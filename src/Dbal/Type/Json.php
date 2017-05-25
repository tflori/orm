<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

/**
 * Json data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Json extends Type
{
    public function validate($value)
    {
        if (is_string($value) && ($value === 'null' || json_decode($value) !== null)) {
            return true;
        }

        return false;
    }
}
