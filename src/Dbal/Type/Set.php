<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error\NotAllowed;

/**
 * Set data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Set extends Enum
{
    protected $type = 'set';

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_string($value)) {
            return new NoString([ 'type' => 'set' ]);
        } else {
            $values = explode(',', $value);
            foreach ($values as $value) {
                if (!in_array($value, $this->allowedValues)) {
                    return new NotAllowed([ 'value' => $value, 'type' => 'set' ]);
                }
            }
        }

        return true;
    }
}
