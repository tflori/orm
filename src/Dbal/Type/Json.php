<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Error\InvalidJson;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Type;

/**
 * Json data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Json extends Type
{
    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_string($value)) {
            return new NoString([ 'type' => 'json' ]);
        } elseif ($value !== 'null' && json_decode($value) === null) {
            return new InvalidJson([ 'value' => (string) $value ]);
        }

        return true;
    }
}
