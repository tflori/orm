<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Type;

/**
 * Set data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Set extends Enum
{
    public function validate($value)
    {
        if (is_string($value)) {
            $values = explode(',', $value);
            if (count(array_diff($values, $this->allowedValues)) === 0) {
                return true;
            }
        }

        return false;
    }
}
