<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class Integer extends Type
{
    /** The types that are integers
     * @var string[] */
    protected static $integerTypes = [
        'serial',
        'bigserial',
        'smallint',
        'integer',
        'bigint',
        'tinyint',
        'mediumint',
        'int',
    ];

    public static function isType($name, $type, $length = null)
    {
        return in_array($type, self::$integerTypes);
    }
}
