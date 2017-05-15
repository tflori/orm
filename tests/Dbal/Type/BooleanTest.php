<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Boolean;
use ORM\Test\TestCase;

class BooleanTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Boolean::class));
    }
}
