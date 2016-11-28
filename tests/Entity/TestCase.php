<?php

namespace ORM\Test\Entity;

use ORM\Test\Entity\Examples\TestEntity;

class TestCase extends \ORM\Test\TestCase
{
    public function setUp()
    {
        parent::setUp();
        TestEntity::reset();
    }
}
