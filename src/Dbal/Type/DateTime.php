<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NoDateTime;
use ORM\Dbal\Type;

/**
 * Date and datetime data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class DateTime extends Type
{
    const DATE_REGEX = '(\+|-)?\d{4,}-\d{2}-\d{2}';
    const TIME_REGEX = '\d{2}:\d{2}:\d{2}(\.\d{1,6})?';
    const ZONE_REGEX = '((\+|-)\d{1,2}(:?\d{2})?|Z)?';

    /** @var int */
    protected $precision;

    /** @var string */
    protected $regex;

    /**
     * DateTime constructor
     *
     * @param int  $precision
     * @param bool $dateOnly
     */
    public function __construct($precision = null, $dateOnly = false)
    {
        $this->precision = (int) $precision;
        $this->regex     = $dateOnly ?
            '/^' . self::DATE_REGEX . '([ T]' . self::TIME_REGEX . self::ZONE_REGEX . ')?$/' :
            '/^' . self::DATE_REGEX . '[ T]' . self::TIME_REGEX . self::ZONE_REGEX . '$/';
    }

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static(
            $columnDefinition['datetime_precision'],
            strpos($columnDefinition['data_type'], 'time') === false
        );
    }

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!$value instanceof \DateTime && (!is_string($value) || !preg_match($this->regex, $value))) {
            return new NoDateTime([ 'value' => (string) $value ]);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }
}
