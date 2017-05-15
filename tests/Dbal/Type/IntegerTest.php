<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Integer;
use ORM\Test\TestCase;

class IntegerTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Integer::class));
    }
}
