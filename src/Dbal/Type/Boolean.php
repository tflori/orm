<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Type;

/**
 * Boolean data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Boolean extends Type
{
    /** @var string */
    protected $true;

    /** @var string */
    protected $false;

    /**
     * Boolean constructor.
     *
     * @param string $true
     * @param string $false
     */
    public function __construct($true, $false)
    {
        $this->true = $true[0] === '\'' && substr($true, -1) === '\'' ?
            substr($true, 1, -1) : $true;
        $this->false = $false[0] === '\'' && substr($false, -1) === '\'' ?
            substr($false, 1, -1) : $false;
    }


    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static($dbal::getBooleanTrue(), $dbal::getBooleanFalse());
    }

    public function validate($value)
    {
        if (is_bool($value)) {
            return true;
        }

        if (is_int($value)) {
            $value = (string)$value;
        }

        if (is_string($value) && ($value === $this->true || $value === $this->false)) {
            return true;
        }

        return false;
    }
}
