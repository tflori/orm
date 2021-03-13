<?php

namespace ORM\Entity;

use ORM\EntityManager as EM;
use ORM\Helper;

trait Booting
{
    /** @var bool[] */
    protected static $booted = [];

    /**
     * Boot the class if it not already booted
     */
    public static function bootIfNotBooted()
    {
        if (isset(static::$booted[static::class])) {
            return;
        }

        static::$booted[static::class] = true;
        static::boot();
    }

    /**
     * Boot the class
     */
    protected static function boot()
    {
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
