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
    public function returnsAStringIfTheEntityIsAlreadyInTheOutput()
    {
        $article1 = new Article(['id' => 1, 'text' => 'Lorem ipsum dolor sit amet...']);
        $article2 = new Article(['text' => 'unsaved article']);
        $category1 = new Category(['id' => 1, 'name' => 'Science']);
        $category2 = new Category(['name' => 'Fiction']);

        self::setProtectedProperty($article1, 'relatedObjects', ['categories' => [$category1, $category2]]);
        self::setProtectedProperty($article2, 'relatedObjects', ['categories' => [$category2]]);
        self::setProtectedProperty($category1, 'relatedObjects', ['articles' => [$article1]]);
        self::setProtectedProperty($category2, 'relatedObjects', ['articles' => [$article1, $article2]]);

        $result = $article1->toArray();

        self::assertSame([
            'id' => 1,
            'text' => 'Lorem ipsum dolor sit amet...',
            'intro' => 'Lorem ipsum dolor sit amet...',
            'categories' => [
                [
                    'id' => 1,
                    'name' => 'Science',
                    'articles' => ['[RECURSION] ' . Article::class . ':1'],
                ],
                [
                    'name' => 'Fiction',
                    'articles' => [
                        '[RECURSION] ' . Article::class . ':1',
                        [
                            'text' => 'unsaved article',
                            'intro' => 'unsaved article',
                            'categories' => ['[RECURSION] ' . Category::class . ':' . spl_object_hash($category2)]
                        ]
                    ]
                ],
            ]
        ], $result);
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

        self::asserTrue(is_array($result['categories'][0]));
        self::assertArraySubset(['name' => 'Science'], $result['categories'][0]);
        self::assertTrue(is_array($result['writer']));
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
