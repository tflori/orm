<?php

namespace ORM\Test;

use ORM\BulkInsert;
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

        static::$resolver = null;
    }

    public function setBulkInsert($class, BulkInsert $bulkInsert)
    {
        $this->bulkInserts[$class] = $bulkInsert;
    }
}
