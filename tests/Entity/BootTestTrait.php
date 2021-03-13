<?php

namespace ORM\Test\Entity;

trait BootTestTrait
{
    /** @var callable */
    public static $bootTestTraitSpy;

    protected static function bootBootTestTrait()
    {
        if (is_callable(static::$bootTestTraitSpy)) {
            call_user_func_array(static::$bootTestTraitSpy, func_get_args());
        }
    }
}
