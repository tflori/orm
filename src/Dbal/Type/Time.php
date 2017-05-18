<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

/**
 * Time data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Time extends Type
{
    /** @var int */
    protected $precision;

    /**
     * DateTime constructor.
     *
     * @param int $precision
     */
    public function __construct($precision = null)
    {
        $this->precision = (int)$precision;
    }

    public static function factory($columnDefinition)
    {
        return new static($columnDefinition['datetime_precision']);
    }
}
