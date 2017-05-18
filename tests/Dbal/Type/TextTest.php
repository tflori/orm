<?php

namespace ORM\Test\Dbal\Type;

use ORM\Dbal\Type\Text;
use ORM\Test\TestCase;

class TextTest extends TestCase
{
    public function testExists()
    {
        self::assertTrue(class_exists(Text::class));
    }
}
