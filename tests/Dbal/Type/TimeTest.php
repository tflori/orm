<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Time;
use ORM\Test\TestCase;

class TimeTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Time::class));
    }
}
