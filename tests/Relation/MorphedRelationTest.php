<?php

namespace ORM\Test\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidRelation;
use ORM\Exception\InvalidType;
use ORM\Relation;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\Image;
use ORM\Test\Entity\Examples\Tag;
use ORM\Test\Entity\Examples\User;
use ORM\Test\TestCase;

class MorphedRelationTest extends TestCase
{
    /** @test */
    public function createFromShortForm()
    {
        $relation = Relation::createRelation(static::class, 'parent', [
            ['parentType' => Entity::class],
            ['parentId'],
        ]);

        self::assertInstanceOf(Relation\Morphed::class, $relation);
    }

    /** @test */
    public function createFromAssocForm()
    {
        $relation = Relation::createRelation(static::class, 'parent', [
            Relation::OPT_MORPH_COLUMN => 'parentType',
            Relation::OPT_MORPH => Entity::class,
            Relation::OPT_REFERENCE => ['parentId' => 'id'],
        ]);

        self::assertInstanceOf(Relation\Morphed::class, $relation);
    }

    /** @test */
    public function createFromShortWithMorphMapAndCardinality()
    {
        $relation = Relation::createRelation(static::class, 'parent', [
            Relation::CARDINALITY_ONE,
            ['parentType' => [
                'article' => Article::class,
                'image' => Image::class,
            ]],
            ['parentId' => 'id'],
        ]);

        self::assertInstanceOf(Relation\Morphed::class, $relation);
    }

    /** @test */
    public function shortFormOnlyAllowsOneMorphColumn()
    {
        self::expectException(InvalidConfiguration::class);
        self::expectExceptionMessage('Invalid short form');

        $relation = Relation::createRelation(static::class, 'parent', [
            Relation::CARDINALITY_ONE,
            [
                'article' => Article::class,
                'image' => Image::class,
            ],
            ['parentId' => 'id'],
        ]);
    }

    /** @test */
    public function doesNotAllowJoinsFromOwner()
    {
        self::expectException(InvalidRelation::class);
        self::expectExceptionMessage('Morphed relations do not allow joins');

        $fetcher = $this->em->fetch(Tag::class);
        $fetcher->joinRelated('parent');
    }

    /** @test */
    public function fetchReturnsNullWhenTheReferenceIsNotDefined()
    {
        $tag = new Tag(['parent_type' => 'article', 'parent_id' => null]);

        $result = $tag->fetch('parent');

        self::assertNull($result);
    }

    /** @test */
    public function fetchThrowsWhenTheTypeIsNotAllowed()
    {
        $tag = new Tag(['parent_type' => 'user', 'parent_id' => 23]);

        self::expectException(InvalidType::class);
        self::expectExceptionMessage('Reference parent does not support type user');

        $tag->fetch('parent');
    }

    /** @test */
    public function fetchUsesEmFetchWithKeyToGetTheObject()
    {
        $tag = new Tag(['parent_type' => 'article', 'parent_id' => 23]);

        $this->em->shouldReceive('fetch')->with(Article::class, [23])
            ->once()->andReturn(new Article(['id' => 23, 'title' => 'Foo Bar']));

        $tag->fetch('parent');
    }

    /** @test */
    public function fetchingTheRelationWillFilterByTypeAndId()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar']);

        $fetcher = $article->fetch('tags');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "tag" AS t0 ' .
            'WHERE "t0"."parent_type" = \'article\' ' .
            'AND "t0"."parent_id" = 23',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function joiningTheRelationWillFilterTheJoinByTypeAndId()
    {
        $fetcher = $this->em->fetch(Article::class);

        $fetcher->joinRelated('tags');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "article" AS t0 ' .
            'JOIN "tag" AS tags ON ("tags"."parent_type" = \'article\' AND "t0"."id" = "tags"."parent_id")',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function setRelatedThrowsWhenTheMapDoesNotHaveTheClass()
    {
        $tag = new Tag(['name' => 'programming']);

        self::expectException(InvalidType::class);
        self::expectExceptionMessage('Reference parent does not support entities of ');

        $tag->setRelated('parent', new ContactPhone(['id' => 23]));
    }

    /** @test */
    public function setRelatedSetsTypeAndReference()
    {
        $tag = new Tag(['name' => 'programming']);

        $tag->setRelated('parent', new Article(['id' => 23, 'title' => 'Foo Bar']));

        self::assertSame('article', $tag->parentType);
        self::assertSame(23, $tag->parentId);
    }

    /** @test */
    public function setRelatedToNullRemovesTypeAndReference()
    {
        $tag = new Tag(['name' => 'programming', 'parent_type' => 'article', 'parent_id' => 23]);

        $tag->setRelated('parent', null);

        self::assertNull($tag->parentType);
        self::assertNull($tag->parentId);
    }

    /** @test */
    public function setRelatedThrowsWhenReferencedKeyIsNull()
    {
        $tag = new Tag(['name' => 'programming']);

        self::expectException(IncompletePrimaryKey::class);

        $tag->setRelated('parent', new Article(['id' => null, 'title' => 'Foo Bar']));
    }

    // without map but with super

    /** @test */
    public function withoutMapFetchUsesTypeToGetTheEntity()
    {
        $tag = new Tag(['parent_type' => Article::class, 'parent_id' => 23]);

        $this->em->shouldReceive('fetch')->with(Article::class, [23])
            ->once()->andReturn(new Article(['id' => 23, 'title' => 'Foo Bar']));

        $tag->fetch('parentNoMap');
    }

    /** @test */
    public function withoutMapFetchingTheRelationWillFilterByClassAndId()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar']);

        $fetcher = $article->fetch('tagsByClass');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "tag" AS t0 ' .
            'WHERE "t0"."parent_type" = ' . $this->dbal->escapeValue(Article::class) . ' ' .
            'AND "t0"."parent_id" = 23',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function withoutMapJoiningTheRelationWillFilterTheJoinByTypeAndId()
    {
        $fetcher = $this->em->fetch(Article::class);

        $fetcher->joinRelated('tagsByClass');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "article" AS t0 ' .
              'JOIN "tag" AS tagsByClass ON (' .
                '"tagsByClass"."parent_type" = ' . $this->dbal->escapeValue(Article::class) . ' AND ' .
                '"t0"."id" = "tagsByClass"."parent_id"' .
              ')',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function withoutMapThrowsOnSetRelatedWithThatIsNotInstanceOfSuper()
    {
        $tag = new Tag(['name' => 'programming']);

        self::expectException(InvalidType::class);
        self::expectExceptionMessage('Reference parentNoMap does not support entities of ');

        $tag->setRelated('parentNoMap', new User());
    }

    // with different primary keys per morph

    /** @test */
    public function differentPkFetchingTheRelationWillFilterByClassAndId()
    {
        $article = new Article(['article_id' => 23, 'title' => 'Foo Bar']);

        $fetcher = $article->fetch('tagsOverArticleId');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "tag" AS t0 ' .
            'WHERE "t0"."parent_type" = \'article\' ' .
            'AND "t0"."parent_id" = 23',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function differentPkJoiningTheRelationWillFilterTheJoinByTypeAndId()
    {
        $fetcher = $this->em->fetch(Article::class);

        $fetcher->joinRelated('tagsOverArticleId');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "article" AS t0 ' .
              'JOIN "tag" AS tagsOverArticleId ON (' .
                '"tagsOverArticleId"."parent_type" = \'article\' AND ' .
                '"t0"."article_id" = "tagsOverArticleId"."parent_id"' .
              ')',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function differentPkSetRelatedSetsTypeAndReference()
    {
        $tag = new Tag(['name' => 'programming']);

        $tag->setRelated('parentDifferentPk', new Article(['article_id' => 23, 'title' => 'Foo Bar']));

        self::assertSame('article', $tag->parentType);
        self::assertSame(23, $tag->parentId);
    }

    // with different foreign keys per morph

    /** @test */
    public function differentFkFetchingTheRelationWillFilterByClassAndId()
    {
        $article = new Article(['id' => 23, 'title' => 'Foo Bar']);

        $fetcher = $article->fetch('tagsByArticleId');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "tag" AS t0 ' .
            'WHERE "t0"."parent_type" = \'article\' ' .
            'AND "t0"."article_id" = 23',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function differentFkJoiningTheRelationWillFilterTheJoinByTypeAndId()
    {
        $fetcher = $this->em->fetch(Article::class);

        $fetcher->joinRelated('tagsByArticleId');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "article" AS t0 ' .
            'JOIN "tag" AS tagsByArticleId ON (' .
            '"tagsByArticleId"."parent_type" = \'article\' AND ' .
            '"t0"."id" = "tagsByArticleId"."article_id"' .
            ')',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function differentFkSetRelatedRemovesOtherForeignKeys()
    {
        $tag = new Tag(['name' => 'programming', 'parent_type' => 'image', 'image_id' => 42]);

        $tag->setRelated('parentDifferentFk', new Article(['id' => 23, 'title' => 'Foo Bar']));

        self::assertSame('article', $tag->parentType);
        self::assertSame(23, $tag->articleId);
        self::assertNull($tag->imageId);
    }

    /** @test */
    public function differentFkSetRelatedToNullRemovesTypeAndAllReferences()
    {
        $tag = new Tag(['name' => 'programming', 'parent_type' => 'article', 'article_id' => 23, 'image_id' => 42]);

        $tag->setRelated('parentDifferentFk', null);

        self::assertNull($tag->parentType);
        self::assertNull($tag->articleId);
        self::assertNull($tag->imageId);
    }

    // implicit morph maps

    /** @test */
    public function definingReferencesPerClassImpliesAMorphMap()
    {
        $relation = new Relation\Morphed('parentType', Entity::class, [
            Article::class => ['articleId' => 'id'],
            Image::class => ['imageId' => 'id'],
        ]);

        self::expectException(InvalidType::class);

        $relation->setRelated(new Tag(), new User(['id' => 23]));
    }

    /** @test */
    public function definingDifferentPkPerClassImpliesAMorphMap()
    {
        $relation = new Relation\Morphed('parentType', Entity::class, [
            'parentId' => [
                Article::class => 'id',
                Image::class => 'imgId',
            ]
        ]);

        self::expectException(InvalidType::class);

        $relation->setRelated(new Tag(), new User(['id' => 23]));
    }
}
