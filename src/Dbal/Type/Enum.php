<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
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
     * Set constructor.
     *
     * @param \string[] $allowedValues
     */
    public function __construct(array $allowedValues = null)
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

    public function validate($value)
    {
        if (is_string($value) && in_array($value, $this->allowedValues)) {
            return true;
        }

        return false;
    }

    /**
     * @return \string[]
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }
}
