<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Double;
use ORM\Test\TestCase;

class DoubleTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Double::class));
    }
}
