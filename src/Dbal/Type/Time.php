<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Type;

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

    public function validate($value)
    {
        if (is_string($value) && preg_match($this->regex, $value)) {
            return true;
        }

        return false;
    }
}
