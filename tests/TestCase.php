<?php

namespace ORM\Test;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;
use ORM\EntityManager;
use ORM\Test\Entity\Examples\TestEntity;

class TestCase extends MockeryTestCase
{
    /** @var EntityManager|Mock */
    protected $em;

    /** @var \PDO|Mock */
    protected $pdo;

    protected function setUp()
    {
        parent::setUp();
        TestEntity::resetStaticsForTest();
        $this->pdo = \Mockery::mock(\PDO::class);
        $this->em = \Mockery::mock(new EntityManager([
            EntityManager::OPT_DEFAULT_CONNECTION => $this->pdo
        ]));
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->closeMockery();
    }
}
