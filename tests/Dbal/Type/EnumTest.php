<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Enum;
use ORM\Test\TestCase;

class EnumTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Enum::class));
    }
}
