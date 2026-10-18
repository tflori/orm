<?php

namespace ORM\Entity;

use ORM\EntityManager as EM;
use ORM\Helper;

trait Booting
{
    /** @var bool[] */
    protected static $booted = [];

    /** Classes where Entity::boot() got called (to detect a missing parent::boot())
     * @var bool[] */
    private static $entityBooted = [];

    /**
     * Boot the class if it not already booted
     */
    public static function bootIfNotBooted()
    {
        if (isset(static::$booted[static::class])) {
            return;
        }

        static::$booted[static::class] = true;
        $relations = static::$relations;
        static::boot();
        static::storeRelationsFromBoot($relations);

        if (!isset(self::$entityBooted[static::class])) {
            trigger_error(sprintf(
                '%s::boot() has to call parent::boot() - otherwise traits and parent classes are not booted',
                (new \ReflectionMethod(static::class, 'boot'))->getDeclaringClass()->getName()
            ), E_USER_WARNING);
        }
    }

    /**
     * Boot the class
     *
     * Overwrite this method to define relations, observers etc. - always call parent::boot().
     */
    protected static function boot()
    {
        self::$entityBooted[static::class] = true;
        static::bootTraits();
    }

    /**
     * Boot the traits of the class
     */
    protected static function bootTraits()
    {
        foreach (Helper::traitUsesRecursive(static::class) as $trait) {
            $method = EM::getInstance(static::class)->getNamer()
                ->getMethodName('boot' . ucfirst(Helper::shortName($trait)));
            if (method_exists(static::class, $method)) {
                forward_static_call([static::class, $method]);
            }
        }
    }
}
