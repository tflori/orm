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
        self::resetRelationsForTest();
    }

    public static function resetRelationsForTest()
    {
        self::$booted = [];
        foreach (['definedRelations', 'resolvedRelations'] as $property) {
            $reflection = new \ReflectionProperty(Entity::class, $property);
            if (PHP_VERSION_ID < 80100) {
                $reflection->setAccessible(true);
            }
            $reflection->setValue(null, []);
        }
    }
}
