<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Error;
use ORM\Dbal\Error\NoTime;

/**
 * Time data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Time extends DateTime
{
    public function __construct($precision = null)
    {
        parent::__construct($precision);
        $this->regex = '/^' . self::TIME_REGEX . self::ZONE_REGEX . '$/';
    }

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_string($value) || !preg_match($this->regex, $value)) {
            if ($value instanceof \DateTime) {
                return new Error([], 'DATETIME', 'DateTime is not allowed for time');
            }

            return new NoTime([ 'value' => (string) $value ]);
        }

        return true;
    }
}
