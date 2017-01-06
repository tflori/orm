<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class TestEntity extends Entity
{
    public static function resetStaticsForTest()
    {
        self::$namingSchemeTable = 'snake_lower';
        self::$namingSchemeColumn = 'snake_lower';
        self::$namingSchemeMethods = 'camelCase';
        self::$tableNameTemplate = '%short%';
        self::$namingUsed = false;
        self::$reflections = [];
        self::$calculatedTableNames = [];
        self::$calculatedColumnNames = [];
    }

    public static function resetNamingUsed() {
        self::$namingUsed = false;
    }
}
