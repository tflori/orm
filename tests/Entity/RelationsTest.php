<?php

namespace ORM\Test\Entity;

use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\UndefinedRelation;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\Entity\Examples\TestEntity;
use \ORM\Test\TestCase;
use \ORM\Test\Entity\Examples\Relation;
use \ORM\Entity;

class RelationsTest extends TestCase
{
    public function provideRelationDefinitions()
    {
        return [
            [Relation::class, 'studlyCaps', 'one', StudlyCaps::class, ['studlyCapsId' => 'id']],
            [Relation::class, 'psr0StudlyCaps', 'one', Psr0_StudlyCaps::class, ['psr0StudlyCaps' => 'id']],
            [Relation::class, 'testEntities', 'many', TestEntity::class, null, 'relation'],
            [Relation::class, 'dmgd', 'one', DamagedABBRVCase::class, ['dmgdId' => 'id']],
            [DamagedABBRVCase::class, 'relation', 'one', Relation::class, null, 'dmgd'],
            [Snake_Ucfirst::class, 'relations', 'many', Relation::class, null, 'snake'],
            [
                Article::class,
                'categories',
                'many',
                Category::class,
                ['id' => 'articleId'],
                'articles',
                'article_category'
            ],
            [
                Category::class,
                'articles',
                'many',
                Article::class,
                ['id' => 'categoryId'],
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
}
