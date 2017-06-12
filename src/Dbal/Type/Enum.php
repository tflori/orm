<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NoString;
use ORM\Dbal\Error\NotAllowed;
use ORM\Dbal\Type;

/**
 * Enum data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Enum extends Type
{
    /** @var string[] */
    protected $allowedValues = null;

    /**
     * Set constructor
     *
     * @param string[] $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        $allowedValues = [];
        if (!empty($columnDefinition['enumeration_values'])) {
            $allowedValues = explode('\',\'', substr($columnDefinition['enumeration_values'], 1, -1));
        }

        return new static($allowedValues);
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
            return new NoString([ 'type' => 'enum' ]);
        } elseif (!in_array($value, $this->allowedValues)) {
            return new NotAllowed([ 'value' => $value, 'type' => 'enum' ]);
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }
}
