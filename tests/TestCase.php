<?php

namespace ORM\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;

class TestCase extends MockeryTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        $this->closeMockery();
    }
}
