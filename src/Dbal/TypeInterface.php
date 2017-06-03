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
     * Create Type class for given $dbal and $columnDefinition
     *
     * @param Dbal $dbal
     * @param array $columnDefinition
     * @return Type
     */
    public static function factory(Dbal $dbal, array $columnDefinition);

    /**
     * Check if this type fits to $columnDefinition
     *
     * @param array $columnDefinition
     * @return boolean
     */
    public static function fits(array $columnDefinition);

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value);
}
