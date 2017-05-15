<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\VarChar;
use ORM\Test\TestCase;

class VarCharTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(VarChar::class));
    }
}
