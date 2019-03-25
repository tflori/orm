<?php

namespace ORM\Test\Examples;

use DateTimeZone;

class DateTimeDerivate extends \DateTime
{
    /**
     * @param string $format
     * @param string $time
     * @param DateTimeZone|null $timezone
     * @return static
     */
    public static function createFromFormat($format, $time, DateTimeZone $timezone = null)
    {
        $dt = parent::createFromFormat($format, $time, $timezone);
        return new static($dt->format('Y-m-d H:i:s.u'), $dt->getTimezone());
    }
}
