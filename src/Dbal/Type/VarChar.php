<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error\TooLong;
use ORM\Dbal\Type;

/**
 * String data type
 *
 * With and without max / fixed length
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class VarChar extends Type
{
    /** @var int */
    protected $maxLength;

    /** @var string */
    protected $type = 'varchar';

    /**
     * VarChar constructor
     *
     * @param int $maxLength
     */
    public function __construct($maxLength = null)
    {
        $this->maxLength = (int) $maxLength;
    }

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static($columnDefinition['character_maximum_length']);
    }

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_string($value)) {
            return new NoString([ 'type' => $this->type ]);
        } elseif ($this->maxLength !== 0 && mb_strlen($value) > $this->maxLength) {
            return new TooLong([ 'value' => $value, 'max' => $this->maxLength ]);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }
}
