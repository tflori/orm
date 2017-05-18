<?php

namespace ORM\Dbal;

/**
 * Interface TypeInterface
 *
 * @package ORM\Dbal
 */
interface TypeInterface
{
    /**
     * Create this type from $columnDefinition.
     *
     * Returns null when column definition does not match.
     *
     * @param $columnDefinition
     * @return TypeInterface
     */
    public static function fromDefinition($columnDefinition);
}
