<?php

namespace ORM\Test\Entity;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidRelation;
use ORM\Exceptions\UndefinedRelation;
use ORM\Exceptions\NoEntityManager;
use ORM\Relation\ManyToMany;
use ORM\Relation\OneToMany;
use ORM\Relation\OneToOne;
use ORM\Relation\Owner;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\EntityFetcher;

class RelationsTest extends TestCase
{
    public function provideRelationDefinitions()
    {
        return [
            [
                RelationExample::class,
                'studlyCaps',
                Owner::class,
                StudlyCaps::class,
                ['studlyCapsId' => 'id']
            ],
            [
                RelationExample::class,
                'psr0StudlyCaps',
                Owner::class,
                Psr0_StudlyCaps::class,
                ['psr0StudlyCaps' => 'id']
            ],
            [
                RelationExample::class,
                'contactPhones',
                OneToMany::class,
                ContactPhone::class,
                null,
                'relation'
            ],
            [
                RelationExample::class,
                'dmgd',
                Owner::class,
                DamagedABBRVCase::class,
                ['dmgdId' => 'id']
            ],
            [
                DamagedABBRVCase::class,
                'relation',
                OneToOne::class,
                RelationExample::class,
                null,
                'dmgd'
            ],
            [
                Snake_Ucfirst::class,
                'relations',
                OneToMany::class,
                RelationExample::class,
                null,
                'snake'
            ],
            [
                Article::class,
                'categories',
                ManyToMany::class,
                Category::class,
                ['id' => 'article_id'],
                'articles',
                'article_category'
            ],
            [
                Category::class,
                'articles',
                ManyToMany::class,
                Article::class,
                ['id' => 'category_id'],
                'categories',
                'article_category'
            ],
        ];
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testGetRelationAlwaysHasClassAndCardinality($class, $relation, $type, $related)
    {
        $relationDefinition = $class::getRelation($relation);

        self::assertInstanceOf($type, $relationDefinition);
        self::assertSame($related, $relationDefinition->getClass());
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testGetRelationReturnsTheSameObject($class, $relation)
    {
        $result1 = $class::getRelation($relation);
        $result2 = $class::getRelation($relation);

        self::assertSame($result1, $result2);
    }

    public function testThrowsWhenRelationUndefined()
    {
        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation undefinedRel is not defined');

        RelationExample::getRelation('undefinedRel');
    }

    public function testThrowsWhenShortFormIsInvalid()
    {
        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Invalid short form for relation invalid');

        RelationExample::getRelation('invalid');
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testShortRelationDefinitionReference($class, $relation, $cardinality, $related, $reference)
    {
        if (!$reference) {
            return;
        }

        $relationDefinition = $class::getRelation($relation);

        self::assertSame($reference, $relationDefinition->getReference());
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testShortRelationDefinitionOpponent(
        $class,
        $relation,
        $cardinality,
        $related,
        $reference,
        $opponent = null
    ) {
        if (!$opponent) {
            return;
        }
        $opponentRelation = $related::getRelation($opponent);

        $relationDefinition = $class::getRelation($relation);

        self::assertSame($opponentRelation, $relationDefinition->getOpponent());
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testShortRelationDefinitionTable(
        $class,
        $relation,
        $cardinality,
        $related,
        $reference,
        $opponent = null,
        $table = null
    ) {
        if (!$table) {
            return;
        }

        $relationDefinition = $class::getRelation($relation);

        self::assertSame($table, $relationDefinition->getTable());
    }

    public function testFetchRequiresEntityManager()
    {
        $entity = new Article();

        self::expectException(NoEntityManager::class);
        self::expectExceptionMessage('No entity manager given');

        $entity->fetch('categories');
    }

    public function testFetchUsesEntityManagerFromConstruct()
    {
        $entity = new Article(['id' => 42], $this->em);

        $fetcher = $entity->fetch('categories');

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    public function provideRelationsWithCardinalityOne()
    {
        return [
            [RelationExample::class, 'dmgd'],
            [RelationExample::class, 'mySnake'],
        ];
    }

    /**
     * @dataProvider provideRelationsWithCardinalityOne
     */
    public function testGetRelatedReturnsResultFromFetchFor($class, $relation)
    {
        $entity = \Mockery::mock($class)->makePartial();
        $related = new StudlyCaps();
        $entity->shouldReceive('fetch')->with($relation, null, true)->once()->andReturn($related);

        $result = $entity->getRelated($relation);

        self::assertSame($related, $result);
    }

    /**
     * @dataProvider provideRelationsWithCardinalityOne
     */
    public function testGetRelatedStoresTheValue($class, $relation)
    {
        $entity = \Mockery::mock($class)->makePartial();
        $related = new StudlyCaps();
        $entity->shouldReceive('fetch')->with($relation, null, true)->once()->andReturn($related);
        $entity->getRelated($relation);

        $result = $entity->getRelated($relation);

        self::assertSame($related, $result);
    }

    /**
     * @dataProvider provideRelationsWithCardinalityOne
     */
    public function testRefreshsRelationWithRefresh($class, $relation)
    {
        $entity = \Mockery::mock($class)->makePartial();
        $related = new StudlyCaps();
        $entity->shouldReceive('fetch')->with($relation, null, true)->twice()->andReturn($related);
        $entity->getRelated($relation);

        $result = $entity->getRelated($relation, true);

        self::assertSame($related, $result);
    }

    /**
     * @dataProvider provideRelationsWithCardinalityOne
     */
    public function testGetRelatedDoesNotStoreNullValues($class, $relation)
    {
        $entity = \Mockery::mock($class)->makePartial();
        $related = new StudlyCaps();
        $entity->shouldReceive('fetch')->with($relation, null, true)->twice()->andReturn(null, $related);
        $entity->getRelated($relation);

        $result = $entity->getRelated($relation);

        self::assertSame($related, $result);
    }

    public function testSetRelationStoresTheId()
    {
        $entity = new RelationExample();
        $related = new StudlyCaps(['id' => 42]);

        $entity->setRelated('studlyCaps', $related);

        self::assertSame(42, $entity->studlyCapsId);
    }

    public function testSetRelationThrowsWhenKeyIsIncomplete()
    {
        $entity = new RelationExample();
        $related = new StudlyCaps();

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->setRelated('studlyCaps', $related);
    }

    public function testSetRelationThrowsWhenClassWrong()
    {
        $entity = new RelationExample();

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Invalid entity for relation studlyCaps');

        $entity->setRelated('studlyCaps', new Psr0_StudlyCaps(['id' => 42]));
    }

    public function testSetRelationThrowsForNonOwner()
    {
        $entity = new DamagedABBRVCase();

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('This is not the owner of the relation');

        $entity->setRelated('relation', new RelationExample());
    }

    public function testSetRelationStoresTheRelatedObject()
    {
        $entity = \Mockery::mock(RelationExample::class)->makePartial();
        $related = new StudlyCaps(['id' => 42]);
        $entity->shouldNotReceive('fetch')->with('studlyCaps', null, true);
        $entity->setRelated('studlyCaps', $related);

        $result = $entity->getRelated('studlyCaps');

        self::assertSame($related, $result);
    }

    public function testSetRelationAllowsNull()
    {
        $entity = new RelationExample([], $this->em);
        $related = new StudlyCaps(['id' => 42]);
        $entity->setRelated('studlyCaps', $related);

        $entity->setRelated('studlyCaps', null);

        self::assertNull($entity->studlyCapsId);
        self::assertNull($entity->getRelated('studlyCaps'));
    }

    public function testAddRelationsCreatesTheAssociation()
    {
        $article = new Article(['id' => 42], $this->em);
        $category = new Category(['id' => 23]);
        $this->pdo->shouldReceive('query')
            ->with('INSERT INTO "article_category" ("article_id","category_id") VALUES (42,23)')
            ->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $article->addRelated('categories', [$category]);
    }

    public function testAddRelationsCreatesAMultilineInsert()
    {
        $article = new Article(['id' => 42], $this->em);
        $category1 = new Category(['id' => 23]);
        $category2 = new Category(['id' => 24]);
        $this->pdo->shouldReceive('query')
                  ->with('INSERT INTO "article_category" ("article_id","category_id") VALUES (42,23),(42,24)')
                  ->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $article->addRelated('categories', [$category1, $category2]);
    }

    public function testAddRelationsThrowsWhenClassWrong()
    {
        $article = new Article(['id' => 42], $this->em);

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Invalid entity for relation categories');

        $article->addRelated('categories', [new Category(['id' => 23]), new StudlyCaps()]);
    }

    public function testAddRelationsThrowsWhenRelationIsNotManyToMany()
    {
        $entity = new RelationExample([], $this->em);

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('This is not a many-to-many relation');

        $entity->addRelated('studlyCaps', [new StudlyCaps(['id' => 23])]);
    }

    public function testAddRelationsThrowsWhenEntityHasNoKey()
    {
        $entity = new Article([], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->addRelated('categories', [new Category(['id' => 23])]);
    }

    public function testAddRelationsThrowsWhenARelationHasNoKey()
    {
        $entity = new Article(['id' => 42], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->addRelated('categories', [new Category(['id' => 23]), new Category()]);
    }

    public function testAddRelationsDoesNothingWithEmptyArray()
    {
        $entity = new Article(['id' => 42], $this->em);
        $this->pdo->shouldNotReceive('query');

        $entity->addRelated('categories', []);
    }

    public function testDeleteRelationsDeletesTheAssociation()
    {
        $article = new Article(['id' => 42], $this->em);
        $category = new Category(['id' => 23]);
        $this->pdo->shouldReceive('query')
            ->with('DELETE FROM "article_category" WHERE "article_id" = 42 AND ("category_id" = 23)')
            ->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $article->deleteRelations('categories', [$category]);
    }

    public function testDeleteRelationsExecutesOnlyOneStatement()
    {
        $article = new Article(['id' => 42], $this->em);
        $category1 = new Category(['id' => 23]);
        $category2 = new Category(['id' => 24]);
        $this->pdo->shouldReceive('query')
                  ->with('DELETE FROM "article_category" WHERE "article_id" = 42 ' .
                         'AND ("category_id" = 23 OR "category_id" = 24)')
                  ->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $article->deleteRelations('categories', [$category1, $category2]);
    }

    public function testDeleteRelationsThrowsWhenClassWrong()
    {
        $article = new Article(['id' => 42], $this->em);

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Invalid entity for relation categories');

        $article->deleteRelations('categories', [new Category(['id' => 23]), new StudlyCaps()]);
    }

    public function testDeleteRelationsThrowsWhenRelationIsNotManyToMany()
    {
        $entity = new RelationExample([], $this->em);

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('This is not a many-to-many relation');

        $entity->deleteRelations('studlyCaps', [new StudlyCaps(['id' => 23])]);
    }

    public function testDeleteRelationsThrowsWhenEntityHasNoKey()
    {
        $entity = new Article([], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->deleteRelations('categories', [new Category(['id' => 23])]);
    }

    public function testDeleteRelationsThrowsWhenARelationHasNoKey()
    {
        $entity = new Article(['id' => 42], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->deleteRelations('categories', [new Category(['id' => 23]), new Category()]);
    }

    public function testDeleteRelationsDoesNothingWithEmptyArray()
    {
        $entity = new Article(['id' => 42], $this->em);
        $this->pdo->shouldNotReceive('query');

        $entity->deleteRelations('categories', []);
    }
}
