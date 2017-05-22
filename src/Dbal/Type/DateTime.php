<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Type;

/**
 * Date and datetime data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class DateTime extends Type
{
    const DATE_TIME_REGEX = '/\d{4}-\d{2}-\d{2}( |T)\d{2}:\d{2}:\d{2}((\+|-)\d{1,2}:?\d{2})?/';

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

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static($columnDefinition['datetime_precision']);
    }

    public function validate($value)
    {
        if ($value instanceof \DateTime) {
            return true;
        }

        if (is_string($value) && preg_match(self::DATE_TIME_REGEX, $value)) {
            return true;
        }

        return false;
    }
}
