<?php

namespace ORM\Test\Relation;

use ORM\Relation;
use ORM\Relation\ParentChildren;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestCase;

class ParentChildrenTest extends TestCase
{
    /** @test */
    public function getsReturnedByGetRelation()
    {
        $result = Category::getRelation('children');

        self::assertInstanceOf(ParentChildren::class, $result);
    }

    /** @test */
    public function buildsATreeFromGivenEntities()
    {
        $entities = [
            $category1 = new Category(['id' => 1, 'parent_id' => null, 'name' => 'Category 1']),
            $category2 = new Category(['id' => 2, 'parent_id' => null, 'name' => 'Category 2']),
            $category11 = new Category(['id' => 3, 'parent_id' => 1, 'name' => 'Category 1.1']),
            $category12 = new Category(['id' => 4, 'parent_id' => 1, 'name' => 'Category 1.2']),
            $category111 = new Category(['id' => 5, 'parent_id' => 3, 'name' => 'Category 1.1.1']),
        ];

        $this->em->shouldNotReceive('fetch');

        $tree = Category::getRelation('children')->buildTree(...$entities);

        self::assertSame([$category1, $category2], $tree);
        self::assertSame([$category11, $category12], $category1->children);
        self::assertSame([$category111], $category11->children);
    }

    /** @test */
    public function canBeGeneratedFromAssocForm()
    {
        $relation = Relation::createRelation(Category::class, 'children', [
            Relation::OPT_CLASS => Category::class,
            Relation::OPT_OPPONENT => 'parent',
        ]);

        self::assertInstanceOf(ParentChildren::class, $relation);
    }
}
