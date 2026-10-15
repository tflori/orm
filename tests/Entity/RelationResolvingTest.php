<?php

namespace ORM\Test\Entity;

use Mockery as m;
use ORM\Entity;
use ORM\Relation\Owner;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\BootArticle;
use ORM\Test\Entity\Examples\BootArticleChild;
use ORM\Test\Entity\Examples\BootArticleOverride;
use ORM\Test\Entity\Examples\BootMembership;
use ORM\Test\Entity\Examples\BootShare;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\Membership;
use ORM\Test\Entity\Examples\Share;
use ORM\Test\Entity\Examples\SpecialArticle;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;
use ORM\Test\TestEntity;

class RelationResolvingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestEntity::resetRelationsForTest();
    }

    protected static function getSharedRelations()
    {
        $reflection = new \ReflectionProperty(Entity::class, 'relations');
        $reflection->setAccessible(true);
        return $reflection->getValue();
    }

    public function provideEntitiesWithRelationRole()
    {
        return [
            '<name>Relation()' => [Share::class, Membership::class],
            'boot()' => [BootShare::class, BootMembership::class],
        ];
    }

    /** @dataProvider provideEntitiesWithRelationRole
     * @test */
    public function entitiesSharingTheRelationsPropertyGetSeparateRelations($classA, $classB)
    {
        $sharedRelations = self::getSharedRelations();

        $relationA = $classA::getRelation('role');
        $relationB = $classB::getRelation('role');

        self::assertNotSame($relationA, $relationB);
        self::assertSame($classA, self::getProtectedProperty($relationA, 'parent'));
        self::assertSame(User::class, self::getProtectedProperty($relationA, 'class'));
        self::assertSame($classB, self::getProtectedProperty($relationB, 'parent'));
        self::assertSame(Category::class, self::getProtectedProperty($relationB, 'class'));
        self::assertSame($sharedRelations, self::getSharedRelations());
    }

    /** @test */
    public function relationInstancesFromDefinitionsAreNotBoundDirectly()
    {
        $relation = BootMembership::getRelation('role');

        $definedRelations = new \ReflectionProperty(Entity::class, 'definedRelations');
        $definedRelations->setAccessible(true);
        $definition = $definedRelations->getValue()[BootMembership::class]['role'];

        self::assertNotSame($definition, $relation);
        self::assertNull(self::getProtectedProperty($definition, 'parent'));
    }

    public function provideCallOrder()
    {
        return [
            'parent first' => [true],
            'subclass first' => [false],
        ];
    }

    /** @dataProvider provideCallOrder
     * @test */
    public function subclassesGetTheirOwnRelation($parentFirst)
    {
        if ($parentFirst) {
            $parentRelation = Article::getRelation('writer');
            $relation = SpecialArticle::getRelation('writer');
        } else {
            $relation = SpecialArticle::getRelation('writer');
            $parentRelation = Article::getRelation('writer');
        }

        self::assertNotSame($parentRelation, $relation);
        self::assertSame(SpecialArticle::class, self::getProtectedProperty($relation, 'parent'));
        self::assertSame(Article::class, self::getProtectedProperty($parentRelation, 'parent'));
    }

    /** @test */
    public function subclassesInheritRelationsFromBootOfTheirParent()
    {
        $relation = BootArticleChild::getRelation('writer');

        self::assertSame(BootArticleChild::class, self::getProtectedProperty($relation, 'parent'));
        self::assertSame(User::class, self::getProtectedProperty($relation, 'class'));
    }

    /** @test */
    public function subclassesCanOverrideRelationsFromBootOfTheirParent()
    {
        $relation = BootArticleOverride::getRelation('writer');
        $parentRelation = BootArticle::getRelation('writer');

        self::assertSame(Category::class, self::getProtectedProperty($relation, 'class'));
        self::assertSame(User::class, self::getProtectedProperty($parentRelation, 'class'));
    }

    /** @dataProvider provideCallOrder
     * @test */
    public function mockedEntitiesUseTheRelationOfTheMockedClass($classFirst)
    {
        $mock = m::mock(Article::class)->makePartial();

        if ($classFirst) {
            $relation = Article::getRelation('writer');
            $mockRelation = $mock::getRelation('writer');
        } else {
            $mockRelation = $mock::getRelation('writer');
            $relation = Article::getRelation('writer');
        }

        self::assertSame($relation, $mockRelation);
        self::assertSame(Article::class, self::getProtectedProperty($relation, 'parent'));
    }

    /** @test */
    public function joiningAMorphedRelationWorksAfterAMockUsedTheRelation()
    {
        $mock = m::mock(Article::class)->makePartial();
        $mock::getRelation('tags');

        $fetcher = $this->em->fetch(Article::class);
        $fetcher->joinRelated('tags');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "article" AS t0 ' .
            'JOIN "tag" AS tags ON ("tags"."parent_type" = \'article\' AND "t0"."id" = "tags"."parent_id")',
            $fetcher->getQuery()
        );
    }

    public function provideRelationDefinitionStyles()
    {
        return [
            '$relations' => [Article::class, 'writer', User::class, ['user_id' => 1]],
            '<name>Relation()' => [Share::class, 'role', User::class, ['user_id' => 1]],
            'boot() with $relations' => [BootShare::class, 'role', User::class, ['user_id' => 1]],
            'boot() with defineRelation()' => [BootMembership::class, 'role', Category::class, ['category_id' => 1]],
            'boot() of parent' => [BootArticleChild::class, 'writer', User::class, ['user_id' => 1]],
        ];
    }

    /** @dataProvider provideRelationDefinitionStyles
     * @test */
    public function magicAccessLoadsTheRelationWithoutResolvingItFirst($class, $relation, $relatedClass, $data)
    {
        $entity = new $class($data, $this->em);
        $related = new $relatedClass(['id' => 1]);
        $this->em->shouldReceive('fetch')->with($relatedClass, [1])->once()->andReturn($related);

        self::assertTrue(isset($entity->$relation));
        self::assertSame($related, $entity->$relation);
    }

    /** @dataProvider provideRelationDefinitionStyles
     * @test */
    public function hasRelationFindsAllDefinitionStyles($class, $relation)
    {
        self::assertTrue($class::hasRelation($relation));
        self::assertFalse($class::hasRelation('undefinedRelation'));
    }

    /** @test */
    public function hasRelationUsesTheMockedClass()
    {
        $mock = m::mock(Share::class)->makePartial();

        self::assertTrue($mock::hasRelation('role'));
    }

    /** @test */
    public function bindDoesNotThrowWhenTheRelationIsBoundAgain()
    {
        $relation = new Owner(User::class, ['userId' => 'id']);
        $relation->bind(Article::class, 'writer');

        $relation->bind(Category::class, 'author');

        self::assertSame(Category::class, self::getProtectedProperty($relation, 'parent'));
        self::assertSame('author', self::getProtectedProperty($relation, 'name'));
    }
}
