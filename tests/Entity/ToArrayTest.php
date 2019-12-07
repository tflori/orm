<?php

namespace ORM\Test\Entity;

use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\GeneratesUuid;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;
use ORM\Testing\MocksEntityManager;

class ToArrayTest extends TestCase
{
    use MocksEntityManager;

    protected function setUp()
    {
        parent::setUp();

        $this->em = $this->ormInitMock();
    }


    /** @test */
    public function returnsAnArray()
    {
        $article = new Article();

        $result = $article->toArray();

        self::assertInternalType('array', $result);
    }

    /** @test */
    public function returnsNullValuesForAliases()
    {
        $entity = new Snake_Ucfirst();

        $result = $entity->toArray();

        self::assertSame(['anotherVar' => null], $result);
    }

    /** @test */
    public function usesTheAttributesPassed()
    {
        $entity = new Article();

        $result = $entity->toArray(['foo', 'bar']);

        self::assertSame(['foo' => null, 'bar' => null], $result);
    }

    /** @test */
    public function getsAttributesFromData()
    {
        $entity = new Article(['title' => 'Foo Bar', 'small_intro' => 'Lorem ipsum dolor sit amet.']);

        $result = $entity->toArray();

        self::assertSame([
            'title' => 'Foo Bar',
            'smallIntro' => 'Lorem ipsum dolor sit amet.',
            'intro' => '',
        ], $result);
    }

    /** @test */
    public function getsValuesUsingTheGetters()
    {
        $entity = new Article([
            'text' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor ' .
                      'invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam ',
            'intro' => 'This is different',
        ]);

        $result = $entity->toArray();

        self::assertArrayHasKey('intro', $result);
        self::assertNotEquals('This is different', $result['intro']);
    }

    /** @test */
    public function includesAttributesFromIncludedAttributes()
    {
        $entity = new Article();

        $result = $entity->toArray();

        self::assertArrayHasKey('intro', $result);
    }

    /** @test */
    public function excludesAttributesFromExcludedAttributes()
    {
        $entity = new Article(['user_id' => 1]);

        $result = $entity->toArray();

        self::assertArrayNotHasKey('userId', $result);
    }

    /** @test */
    public function returnsLoadedRelations()
    {
        $entity = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'text' => 'Lorem ipsum dolor sit amet...']);
        self::setProtectedProperty($entity, 'relatedObjects', [
            'categories' => [
                $this->ormCreateMockedEntity(Category::class, ['name' => 'Science']),
                $this->ormCreateMockedEntity(Category::class, ['name' => 'Fiction']),
            ],
        ]);

        $result = $entity->toArray();

        self::assertArrayHasKey('categories', $result);
        self::assertCount(2, $result['categories']);
    }

    /** @test */
    public function returnsArraysOfRelatedObjects()
    {
        $entity = $this->ormCreateMockedEntity(Article::class, ['id' => 42, 'text' => 'Lorem ipsum dolor sit amet...']);
        self::setProtectedProperty($entity, 'relatedObjects', [
            'categories' => [
                $this->ormCreateMockedEntity(Category::class, ['name' => 'Science']),
            ],
            'writer' => $this->ormCreateMockedEntity(User::class, ['name' => 'John Doe'])
        ]);

        $result = $entity->toArray();

        self::assertInternalType('array', $result['categories'][0]);
        self::assertArraySubset(['name' => 'Science'], $result['categories'][0]);
        self::assertInternalType('array', $result['writer']);
        self::assertArraySubset(['name' => 'John Doe'], $result['writer']);
    }

    /** @test */
    public function usesAliasesForAttributeNames()
    {
        $entity = $this->ormCreateMockedEntity(StaticTableName::class, ['bar' => 42]);

        $result = $entity->toArray();

        self::assertArrayHasKey('foo', $result);
    }

    /** @test */
    public function returnsEmptyArrayWhenNoAttributesAreDefined()
    {
        $entity = new GeneratesUuid();

        $result = $entity->toArray();

        self::assertSame([], $result);
    }
}
