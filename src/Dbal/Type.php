<?php

namespace ORM\Dbal;

abstract class Type implements TypeInterface
{
    /** Data Types that map to this type for basic type matching
     * @var string[] */
    protected static $dataTypes = [];

    public static function isType($name, $type)
    {
        // remove size for mapping
        if (($p = strpos($type, '(')) !== false && $p > 0) {
            $type = substr($type, 0, $p);
        }

        return in_array($type, static::$dataTypes);
    }
}
