<?php

namespace ORM\Test\Dbal\Type\Custom;

use ORM\Dbal\Dbal;

class CustomDbal extends Dbal
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
