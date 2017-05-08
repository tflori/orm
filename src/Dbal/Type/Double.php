<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class Double extends Type
{
    /** The types that are integers
     * @var string[] */
    protected static $doubleTypes = [
        'decimal',
        'float',
        'double',
    ];

    public static function isType($name, $type, $length = null)
    {
        return in_array($type, self::$doubleTypes);
    }
}
