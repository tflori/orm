<?php

namespace ORM\Dbal;

/**
 * Base class for data types
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Type implements TypeInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore void method for types covered by mapping
     */
    public static function fits(array $columnDefinition)
    {
        return false;
    }

    /**
     * Returns a new Type object
     *
     * This method is only for types covered by mapping. Use fromDefinition instead for custom types.
     *
     * @param Dbal  $dbal
     * @param array $columnDefinition
     * @return static
     */
    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static();
    }
}
