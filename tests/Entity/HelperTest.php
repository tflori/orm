<?php

namespace ORM\Test\Entity;

use ORM\EntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class HelperTest extends TestCase
{
    /** @test */
    public function queryReturnsAnEntityFetcher()
    {
        $fetcher = Article::query();

        self::assertEquals(EntityManager::getInstance(Article::class)->fetch(Article::class), $fetcher);
    }
}
