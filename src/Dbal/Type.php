<?php

namespace ORM\Dbal;

abstract class Type implements TypeInterface
{
    public static function fromDefinition($columnDefinitoin)
    {
        return null;
    }

    public static function factory($columnDefinition)
    {
        return new static;
    }
}
