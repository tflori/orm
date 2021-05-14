<?php

namespace ORM;

class Helper
{
    /**
     * Get all traits used by the class and it's parents.
     *
     * Iterates recursively through traits to get traits used by traits.
     *
     * @param string $class
     * @param bool $withParents
     * @return string[]
     */
    public static function traitUsesRecursive($class, $withParents = true)
    {
        $traits = class_uses($class);

        foreach ($traits as $trait) {
            $traits += self::traitUsesRecursive($trait);
        }

        if ($withParents) {
            foreach (class_parents($class) as $parent) {
                $traits += self::traitUsesRecursive($parent, false);
            }
        }

        return array_unique($traits);
    }

    /**
     * Gets the short name of a class without creating a Reflection
     *
     * @param string $class
     * @return string
     */
    public static function shortName($class)
    {
        return basename(str_replace('\\', '/', $class));
    }

    /**
     * Get the first element of $array
     *
     * Returns $default if the array is empty.
     *
     * @param array $array
     * @param mixed $default
     * @return mixed
     */
    public static function first(array $array, $default = null)
    {
        foreach ($array as $k => $v) {
            return $v;
        }
        return $default;
    }
}
