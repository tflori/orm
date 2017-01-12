<?php

namespace ORM\Test\Entity;

use \ORM\Test\TestCase;
use \ORM\Test\Entity\Examples\Relation;
use \ORM\Entity;

class RelationsTest extends TestCase
{
    public function provideRelationDefinitions()
    {
        return [
            ['studlyCaps', 'one'],
            ['psr0StudlyCaps', 'one'],
            ['testEntities', 'many']
        ];
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testGetRelationDefinitionAlwaysHasClassAndCardinality($relation, $cardinality)
    {
        $relationDefinition = Relation::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_CARDINALITY, $relationDefinition);
        self::assertArrayHasKey(Entity::OPT_RELATION_CLASS, $relationDefinition);
        self::assertSame($cardinality, $relationDefinition[Entity::OPT_RELATION_CARDINALITY]);
    }
}
