<?php

namespace ORM\Test\Entity;

use ORM\Exception;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;
use ORM\Test\TestEntityManager;

class BasicTest extends TestCase
{
    /** @test */
    public function creatingEntitiesThrowsWhenNoEntityManagerIsDefined()
    {
        // reset the em for this test
        TestEntityManager::resetStaticsForTest();

        self::expectException(Exception::class);
        self::expectExceptionMessage('No entity manager initialized');

        $article = new Article();
    }
}
