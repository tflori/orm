<?php

namespace ORM\Test\Dbal\Type\Custom;

use ORM\Dbal\Column;

class CustomColumn extends Column
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
