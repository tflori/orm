<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\DateTime;
use ORM\Test\TestCase;

class DateTimeTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(DateTime::class));
    }
}
