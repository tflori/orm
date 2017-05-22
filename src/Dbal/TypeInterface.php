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
     * @param Dbal $dbal
     * @param array $columnDefinition
     * @return mixed
     */
    public static function factory(Dbal $dbal, array $columnDefinition);

    /**
     * Check if this type fits to $columnDefinition.
     *
     * @param array $columnDefinition
     * @return boolean
     */
    public static function fits(array $columnDefinition);

    /**
     * Check if $value is valid for this type.
     *
     * @param mixed $value
     * @return boolean
     */
    public function validate($value);
}
