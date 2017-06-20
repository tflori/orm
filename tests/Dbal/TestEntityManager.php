<?php

namespace ORM\Test\Dbal;

use ORM\EntityManager;

class TestEntityManager extends EntityManager
{
    public static function resetStaticsForTest()
    {
        static::$emMapping = [
            'byClass' => [],
            'byNameSpace' => [],
            'byParent' => [],
            'last' => null,
        ];
    }
}
