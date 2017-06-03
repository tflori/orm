<?php

namespace ORM\Test;

use ORM\Entity;

abstract class TestEntity extends Entity
{
    public static function resetStaticsForTest()
    {
        self::$namingSchemeTable = 'snake_lower';
        self::$namingSchemeColumn = 'snake_lower';
        self::$namingSchemeMethods = 'camelCase';
        self::$tableNameTemplate = '%short%';
        self::$reflections = [];
    }
}
