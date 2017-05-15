<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Set;
use ORM\Test\TestCase;

class SetTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Set::class));
    }
}
