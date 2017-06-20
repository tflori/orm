<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Error;
use ORM\Dbal\Error\NoBoolean;
use ORM\Dbal\Type;

/**
 * Boolean data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Boolean extends Type
{
    /** @var Dbal */
    protected $dbal;

    /**
     * Boolean constructor
     *
     * @param Dbal $dbal
     */
    public function __construct(Dbal $dbal)
    {
        $this->dbal = $dbal;
    }

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static($dbal);
    }

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if (!is_bool($value)) {
            // convert int to string
            if (is_int($value)) {
                $value = (string) $value;
            }

            if (!is_string($value) ||
                ($value !== $this->getBoolean(true) && $value !== $this->getBoolean(false))
            ) {
                // value is not boolean, not int and (not string OR string value for boolean)
                return new NoBoolean([ 'value' => (string) $value ]);
            }
        }

        return true;
    }

    /**
     * Get the string representation for boolean
     *
     * @param bool $bool
     * @return string
     */
    protected function getBoolean($bool)
    {
        $quoted = $this->dbal->escapeValue($bool);
        return $quoted[0] === '\'' ? substr($quoted, 1, -1) : $quoted;
    }
}
