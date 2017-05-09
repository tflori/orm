<?php

namespace ORM\Dbal;

interface TypeInterface
{
    /**
     * Checks if $name and $type matches to this type
     *
     * @param string $name
     * @param string $type
     * @param int    $length
     * @return bool
     */
    public static function isType($name, $type);
}
