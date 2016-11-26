<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class TestEntity extends Entity
{
    public static function reset()
    {
        self::$reflections = [];
        self::$tableNames = [];
    }
}
