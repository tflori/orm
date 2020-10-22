<?php

namespace ORM\Test\Entity;

use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class IssetTest extends TestCase
{
    /** @test */
    public function usesExistingGetters()
    {
        $entity = \Mockery::mock(Article::class)->makePartial();

        $entity->shouldReceive('getIntro')->once()->andReturn('Any text');

        $result = isset($entity->intro);

        self::assertTrue($result);
    }

    /** @test */
    public function loadsTheRelation()
    {
        $entity = \Mockery::mock(Article::class)->makePartial();

        $entity->shouldReceive('getRelated')->with('categories')->andReturn([]);

        $result = isset($entity->categories);

        self::assertFalse($result);
    }

    /** @test */
    public function checksIfValueExists()
    {
        $entity = new Article(['published' => false]);

        $result = isset($entity->published);

        self::assertTrue($result);
    }
}
