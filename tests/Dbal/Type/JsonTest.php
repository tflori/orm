<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Json;
use ORM\Test\TestCase;

class JsonTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Json::class));
    }
}
