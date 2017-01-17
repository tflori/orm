<?php

namespace ORM\Test\Entity;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidRelation;
use ORM\Exceptions\UndefinedRelation;
use ORM\Exceptions\NoEntityManager;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\Entity\Examples\TestEntity;
use ORM\Test\TestCase;
use ORM\Test\Entity\Examples\Relation;
use ORM\Entity;
use ORM\EntityFetcher;

class RelationsTest extends TestCase
{
    public function provideRelationDefinitions()
    {
        return [
            [Relation::class, 'studlyCaps', 'one', StudlyCaps::class, ['studlyCapsId' => 'id']],
            [Relation::class, 'psr0StudlyCaps', 'one', Psr0_StudlyCaps::class, ['psr0StudlyCaps' => 'id']],
            [Relation::class, 'contactPhones', 'many', ContactPhone::class, null, 'relation'],
            [Relation::class, 'dmgd', 'one', DamagedABBRVCase::class, ['dmgdId' => 'id']],
            [DamagedABBRVCase::class, 'relation', 'one', Relation::class, null, 'dmgd'],
            [Snake_Ucfirst::class, 'relations', 'many', Relation::class, null, 'snake'],
            [
                Article::class,
                'categories',
                'many',
                Category::class,
                ['id' => 'article_id'],
                'articles',
                'article_category'
            ],
            [
                Category::class,
                'articles',
                'many',
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
    public function testGetRelationDefinitionAlwaysHasClassAndCardinality($class, $relation, $cardinality, $related)
    {
        $relationDefinition = $class::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_CARDINALITY, $relationDefinition);
        self::assertArrayHasKey(Entity::OPT_RELATION_CLASS, $relationDefinition);
        self::assertSame($cardinality, $relationDefinition[Entity::OPT_RELATION_CARDINALITY]);
        self::assertSame($related, $relationDefinition[Entity::OPT_RELATION_CLASS]);
    }

    public function testThrowsWhenRelationUndefined()
    {
        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation undefinedRel is not defined');

        Relation::getRelationDefinition('undefinedRel');
    }

    public function testThrowsWhenShortFormIsInvalid()
    {
        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Invalid short form for relation invalid');

        Relation::getRelationDefinition('invalid');
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testShortRelationDefinitionReference($class, $relation, $cardinality, $related, $reference)
    {
        if (!$reference) {
            return;
        }

        $relationDefinition = $class::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_REFERENCE, $relationDefinition);
        self::assertSame($reference, $relationDefinition[Entity::OPT_RELATION_REFERENCE]);
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

        $relationDefinition = $class::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_OPPONENT, $relationDefinition);
        self::assertSame($opponent, $relationDefinition[Entity::OPT_RELATION_OPPONENT]);
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

        $relationDefinition = $class::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_TABLE, $relationDefinition);
        self::assertSame($table, $relationDefinition[Entity::OPT_RELATION_TABLE]);
    }

    public function testFetchRequiresEntityManager()
    {
        $entity = new Article();

        self::expectException(NoEntityManager::class);
        self::expectExceptionMessage('No entity manager given');

        $entity->fetch('categories');
    }

    public function testFetchReturnsEntityFetcherForMany()
    {
        $entity = new Article(['id' => 42]);

        $fetcher = $entity->fetch('categories', $this->em);

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    public function testFetchUsesEntityManagerFromConstruct()
    {
        $entity = new Article(['id' => 42], $this->em);

        $fetcher = $entity->fetch('categories');

        self::assertInstanceOf(EntityFetcher::class, $fetcher);
    }

    public function testFetchCreatesFetcherForTheRelatedClass()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = new EntityFetcher($this->em, Category::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    public function testFetchFetchesWithPrimaryKeyFor1T1Owner()
    {
        $entity = new Relation(['dmgd_id' => 42], $this->em);
        $related = new DamagedABBRVCase(['id' => 42]);
        $this->em->shouldReceive('fetch')->with(DamagedABBRVCase::class, [42])->once()->andReturn($related);

        $result = $entity->fetch('dmgd');

        self::assertSame($related, $result);
    }

    public function testFetchReturnsNullWhenReferenceIsEmpty()
    {
        $entity = new Relation([], $this->em);

        $result = $entity->fetch('dmgd');

        self::assertNull($result);
    }

    public function testFetchFiltersByForeignKeyAndReturnsFirstFor1T1()
    {
        $entity = new DamagedABBRVCase(['id' => 42], $this->em);
        $related = new Relation();
        $fetcher = \Mockery::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(Relation::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('dmgdId', 42)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('one')->with()->once()->andReturn($related);

        $result = $entity->fetch('relation');

        self::assertSame($related, $result);
    }

    public function testFetchThrowsWhenOpponentIsNotDefined()
    {
        $entity = new Snake_Ucfirst([], $this->em);

        self::expectException(UndefinedRelation::class);
        self::expectExceptionMessage('Relation snake is not defined');

        $entity->fetch('relations');
    }

    public function testFetchThrowsWhenReferenceInOpponentIsNotDefined()
    {
        $entity = new Snake_Ucfirst([], $this->em);

        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Reference is not defined in opponent');

        $entity->fetch('relation');
    }

    public function testFetchFiltersByForeignKeyFor1TM()
    {
        $entity = new Relation(['id' => 42], $this->em);
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->passthru();

        $result = $entity->fetch('contactPhones');

        self::assertSame($fetcher, $result);
    }


    public function testFetchReturnsAllWithGetAllFor1TM()
    {
        $entity = new Relation(['id' => 42], $this->em);
        $related = [new ContactPhone(), new ContactPhone()];
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $this->em->shouldReceive('fetch')->with(ContactPhone::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('relationId', 42)->once()->passthru();
        $fetcher->shouldReceive('all')->with()->once()->andReturn($related);

        $result = $entity->fetch('contactPhones', null, true);

        self::assertSame($related, $result);
    }

    public function testFetchThrowsWhenKeyIsEmptyFor1TM()
    {
        $entity = new Relation([], $this->em);

        self::expectException(\ORM\Exceptions\IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('contactPhones');
    }

    public function testFetchFiltersByRelationTableForMTM()
    {
        $entity = new Article(['id' => 42], $this->em);
        $fetcher = \Mockery::mock(EntityFetcher::class);
        $this->em->shouldReceive('fetch')->with(Category::class)->once()->andReturn($fetcher);
        $fetcher->shouldReceive('join')
            ->with('"article_category"', '"article_category"."category_id" = t0.id')
            ->once()->andReturn($fetcher);
        $fetcher->shouldReceive('where')->with('"article_category"."article_id"', 42)->once()->andReturn($fetcher);

        $result = $entity->fetch('categories');

        self::assertSame($fetcher, $result);
    }

    public function testFetchThrowsWhenKeyIsEmptyForMTM()
    {
        $entity = new Article([], $this->em);

        self::expectException(\ORM\Exceptions\IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete for join');

        $entity->fetch('categories');
    }

    public function testReturnsAllWithGetAllForMTM()
    {
        $entity = new Article(['id' => 42], $this->em);
        $related = [
            $this->em->map(new Category(['id' => 12])),
            $this->em->map(new Category(['id' => 33])),
        ];
        $ids = array_map(function ($related) {
            return $related->id;
        }, $related);

        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
            ->with('SELECT "category_id" FROM "article_category" WHERE "article_id" = 42')
            ->once()->andReturn($statement);
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_NUM)->once()->andReturn($ids);

        $result = $entity->fetch('categories', null, true);

        self::assertSame($related, $result);
    }

    public function provideRelationsWithCardinalityOne()
    {
        return [
            [Relation::class, 'dmgd'],
            [Relation::class, 'mySnake'],
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
        $entity = new Relation();
        $related = new StudlyCaps(['id' => 42]);

        $entity->setRelation('studlyCaps', $related);

        self::assertSame(42, $entity->studlyCapsId);
    }

    public function testSetRelationThrowsWhenKeyIsIncomplete()
    {
        $entity = new Relation();
        $related = new StudlyCaps();

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->setRelation('studlyCaps', $related);
    }

    public function testSetRelationThrowsWhenClassWrong()
    {
        $entity = new Relation();

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Invalid entity for relation studlyCaps');

        $entity->setRelation('studlyCaps', new Psr0_StudlyCaps(['id' => 42]));
    }

    public function testSetRelationThrowsForNonOwner()
    {
        $entity = new DamagedABBRVCase();

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('This is not the owner of the relation');

        $entity->setRelation('relation', new Relation());
    }

    public function testSetRelationStoresTheRelatedObject()
    {
        $entity = \Mockery::mock(Relation::class)->makePartial();
        $related = new StudlyCaps(['id' => 42]);
        $entity->shouldNotReceive('fetch')->with('studlyCaps', null, true);
        $entity->setRelation('studlyCaps', $related);

        $result = $entity->getRelated('studlyCaps');

        self::assertSame($related, $result);
    }

    public function testSetRelationAllowsNull()
    {
        $entity = new Relation([], $this->em);
        $related = new StudlyCaps(['id' => 42]);
        $entity->setRelation('studlyCaps', $related);

        $entity->setRelation('studlyCaps', null);

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

        $article->addRelations('categories', [$category]);
    }

    public function testAddRelationsCreatesAMultilineInsert()
    {
        $article = new Article(['id' => 42], $this->em);
        $category1 = new Category(['id' => 23]);
        $category2 = new Category(['id' => 24]);
        $this->pdo->shouldReceive('query')
                  ->with('INSERT INTO "article_category" ("article_id","category_id") VALUES (42,23),(42,24)')
                  ->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $article->addRelations('categories', [$category1, $category2]);
    }

    public function testAddRelationsThrowsWhenClassWrong()
    {
        $article = new Article(['id' => 42], $this->em);

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Invalid entity for relation categories');

        $article->addRelations('categories', [new Category(['id' => 23]), new StudlyCaps()]);
    }

    public function testAddRelationsThrowsWhenRelationIsNotManyToMany()
    {
        $entity = new Relation();

        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('This is not a many-to-many relation');

        $entity->addRelations('studlyCaps', [new StudlyCaps(['id' => 23])]);
    }

    public function testAddRelationsThrowsWhenEntityHasNoKey()
    {
        $entity = new Article([], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->addRelations('categories', [new Category(['id' => 23])]);
    }

    public function testAddRelationsThrowsWhenARelationHasNoKey()
    {
        $entity = new Article(['id' => 42], $this->em);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Key incomplete to save foreign key');

        $entity->addRelations('categories', [new Category(['id' => 23]), new Category()]);
    }
}
