<?php

namespace ORM\Test\Entity;

use \ORM\Test\TestCase;
use \ORM\Test\Entity\Examples\Relations;
use \ORM\Entity;

class RelationsTest extends TestCase
{
    public function provideRelationDefinitions()
    {
        return [
            ['studlycaps']
        ];
    }

    /**
     * @dataProvider provideRelationDefinitions
     */
    public function testGetRelationDefinitionAlwaysHasClassAndCardinality($relation)
    {
        $relationDefinition = Relations::getRelationDefinition($relation);

        self::assertArrayHasKey(Entity::OPT_RELATION_CARDINALITY, $relationDefinition);
        self::assertArrayHasKey(Entity::OPT_RELATION_CLASS, $relationDefinition);
    }
}
