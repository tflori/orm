<?php

namespace ORM\Test;

use ORM\Entity;
use ORM\Helper;
use ORM\QueryBuilder\Parenthesis;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Concerns\WithCreated;
use ORM\Test\Entity\Examples\Concerns\WithUpdated;

class HelperTest extends TestCase
{
    /** @test */
    public function returnsTheShortNameOfAClass()
    {
        $result = Helper::shortName(static::class);

        self::assertSame('HelperTest', $result);
    }

    /** @test */
    public function returnsAllTraitsUsed()
    {
        $result = Helper::traitUsesRecursive(Entity::class);

        self::assertSame(class_uses(Entity::class), $result);
    }

    /** @test */
    public function returnsTraitsFromParentClass()
    {
        $result = Helper::traitUsesRecursive(QueryBuilder::class);

        foreach (class_uses(Parenthesis::class) as $trait) {
            self::assertArrayHasKey($trait, $result);
            self::assertSame($trait, $result[$trait]);
        }
    }

    /** @test */
    public function returnsTraitsFromTraits()
    {
        $result = Helper::traitUsesRecursive(Article::class);

        self::assertArrayHasKey(WithCreated::class, $result);
        self::assertArrayHasKey(WithUpdated::class, $result);
    }
}
