<?php

namespace ORM\Dbal;

abstract class Type implements TypeInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore void method for types covered by mapping
     */
    public static function fromDefinition($columnDefinitoin)
    {
        return null;
    }

    public static function factory($columnDefinition)
    {
        return new static;
    }
}
