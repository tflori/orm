<?php

namespace ORM\Test;

use ORM\EntityFetcher;

class TestEntityFetcher extends EntityFetcher
{
    public static function resetGlobalFiltersForTest($class = null)
    {
        if ($class) {
            unset(static::$globalFilters[$class]);
            return;
        }

        static::$globalFilters = [];
    }
}
