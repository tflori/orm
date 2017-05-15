<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class DateTime extends Type
{
    protected static $dataTypes = [
        'date',
        'datetime',
        'timestamp',
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
