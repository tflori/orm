<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class Time extends Type
{
    protected static $dataTypes = [
        'time'
    ];

    protected $precision;

    /**
     * DateTime constructor.
     *
     * @param int $precision
     */
    public function __construct($precision = null)
    {
        $this->precision = $precision;
    }
}
