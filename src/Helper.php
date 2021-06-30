<?php

namespace ORM;

use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidArgument;

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

    /**
     * Get the key (defined by $referenced) from $entity
     *
     * Reference defines the mapping of attributes in $entity to attributes in the referenced object.
     *
     * Example: $entity is an Article and the 'userId' references the 'id' of the user. Then $reference should be
     * `['userId' => 'id']` and the result will be `['id' => 23]`
     *
     * @param array $reference
     * @param Entity $entity
     * @param bool $allowEmpty
     * @return array|null
     * @throws IncompletePrimaryKey when an empty key is not allowed
     */
    public static function getKey(array $reference, Entity $entity, $allowEmpty = true)
    {
        $key = array_combine(
            array_values($reference),
            array_map([$entity, 'getAttribute'], array_keys($reference))
        );
        if (in_array(null, $key, true)) {
            if ($allowEmpty) {
                return null;
            }
            throw new IncompletePrimaryKey('Key incomplete for join');
        }
        return $key;
    }

    /**
     * Get a unique list of keys (defined by $reference) from $entities
     *
     * @see Helper::getKey
     * @param array $reference
     * @param Entity ...$entities
     * @return array|false
     */
    public static function getUniqueKeys(array $reference, Entity ...$entities)
    {
        $keys = array_filter(array_map(function (Entity $entity) use ($reference) {
            return self::getKey($reference, $entity);
        }, $entities));

        return count($keys) > 1 ? self::uniqueArrays($keys) : $keys;
    }

    /**
     * Make an array of arrays unique
     *
     * @todo Only values are taken into account - so an array ['a' => 1] equals ['b' => 1]
     *
     * @param array[] $array
     * @return array
     */
    public static function uniqueArrays(array $array)
    {
        return array_values(array_combine(array_map(function (array $array) {
            return implode('-', $array);
        }, $array), $array));
    }

    /**
     * Create an associative array where the key
     * @param array $array
     * @param $retriever
     * @return array|false
     */
    public static function keyBy(array $array, $retriever)
    {
        $retriever = self::getValueRetriever($retriever);
        return array_combine(array_map($retriever, $array), $array);
    }

    public static function groupBy(array $array, $retriever)
    {
        $retriever = self::getValueRetriever($retriever);
        return array_reduce($array, function ($array, $item) use ($retriever) {
            $key = $retriever($item);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            $array[$key][] = $item;
            return $array;
        }, []);
    }

    public static function pluck(array $array, $retriever)
    {
        $retriever = self::getValueRetriever($retriever);
        return array_map($retriever, $array);
    }

    public static function only(array $array, array $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }

    private static function getValueRetriever($retriever)
    {
        if (!is_string($retriever) && is_callable($retriever)) {
            return $retriever;
        }

        if (is_string($retriever)) {
            return function ($item) use ($retriever) {
                return self::getData($item, $retriever);
            };
        }

        if (is_array($retriever)) {
            return function ($item) use ($retriever) {
                $values = array_map(function ($key) use ($item) {
                    return self::getData($item, $key);
                }, $retriever);
                return implode('-', $values);
            };
        }

        throw new InvalidArgument('Parameter 1 has to be a string, an array or a callable');
    }

    private static function getData($item, $key)
    {
        if ((is_array($item) || $item instanceof \ArrayAccess) && array_key_exists($key, $item)) {
            return isset($item[$key]) ? $item[$key] : null;
        } elseif (is_object($item) && isset($item->{$key})) {
            return $item->{$key};
        }
        return null;
    }
}
