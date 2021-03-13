<?php

namespace ORM\Test\Entity;

use ORM\Entity;

class BootTestEntity extends Entity
{
    use BootTestTrait;

    /** @var callable */
    public static $bootSpy;

    public static function resetBooting()
    {
        static::$booted = [];
        self::$bootSpy = null;
        self::$bootTestTraitSpy = null;
    }

    protected static function boot()
    {
        if (is_callable(static::$bootSpy)) {
            call_user_func_array(static::$bootSpy, func_get_args());
        }

        parent::boot();
    }
}
