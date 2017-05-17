<?php

namespace ORM\Test\Dbal\Type\Custom;

class Dbal extends \ORM\Dbal
{
    public static function getRegisteredTypes()
    {
        return self::$registeredTypes;
    }

    public static function resetRegisteredTypes()
    {
        self::$registeredTypes = [];
    }
}
