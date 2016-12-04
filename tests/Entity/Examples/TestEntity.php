<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class TestEntity extends Entity
{
    public static function resetStaticsForTest()
    {
        self::$reflections = [];
        self::$tableNames = [];
        self::$translatedColumns = [];
    }
}
