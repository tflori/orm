<?php

namespace ORM\Test\EntityFetcher;

use Mockery\Mock;
use Mockery as m;
use ORM\EntityFetcher;
use ORM\Exception\NotJoined;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\Entity\Examples\DamagedABBRVCase;
use ORM\Test\Entity\Examples\RelationExample;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class BasicTest extends TestCase
{
    /** @test */
    public function runsQueryWithoutParameters()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->once()->with('SELECT DISTINCT t0.* FROM "contact_phone" AS t0')
            ->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->andReturn(true);
        $statement->shouldReceive('fetch')->andReturn(false);

        $fetcher->one();
    }

    /** @test */
    public function returnsNullWhenQueryFails()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->andReturn(false);

        $result = $fetcher->one();

        self::assertNull($result);
    }

    /** @test */
    public function returnsNullWhenResultIsEmpty()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->andReturn(true);
        $statement->shouldReceive('fetch')->andReturn(false);

        $result = $fetcher->one();

        self::assertNull($result);
    }

    /** @test */
    public function executesQueryOnce()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT DISTINCT t0.* FROM "contact_phone" AS t0')
            ->andReturn(false);

        $fetcher->one();
        $fetcher->one();
    }

    /** @test */
    public function usesSpecifiedQuery()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT * FROM "contact_phone" WHERE id = 42 AND name = \'mobile\'')
            ->andReturn(false);

        $fetcher->setQuery('SELECT * FROM "contact_phone" WHERE id = 42 AND name = \'mobile\'');
        $fetcher->one();
    }

    /** @test */
    public function acceptsQueryBuilderInterface()
    {
        $query = m::mock(QueryBuilder::class);
        $query->shouldReceive('getQuery')->once()->andReturn('SELECT * FROM foobar');
        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT * FROM foobar')->andReturn(false);
        $fetcher = $this->em->fetch(ContactPhone::class);

        $fetcher->setQuery($query);
        $fetcher->one();
    }

    /** @test */
    public function doesNotReplaceColumnsAndClasses()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('SELECT * FROM contact_phone cp WHERE cp.id = 42 AND name = \'mobile\'')
                  ->andReturn(false);

        $fetcher->setQuery('SELECT * FROM contact_phone cp WHERE cp.id = 42 AND name = \'mobile\'');
        $fetcher->one();
    }

    /** @test */
    public function replacesQuestionmarksWithQuotedValue()
    {
        $fetcher = new EntityFetcher($this->em, ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('SELECT * FROM contact_phone WHERE id = 42 AND name = \'mobile\'')
                  ->andReturn(false);
        $this->em->shouldReceive('escapeValue')->once()->with(42)->andReturn('42');
        $this->em->shouldReceive('escapeValue')->once()->with('mobile')->andReturn('\'mobile\'');

        $fetcher->setQuery('SELECT * FROM contact_phone WHERE id = ? AND name = ?', [42, 'mobile']);
        $fetcher->one();
    }

    /** @test */
    public function returnsAnEntity()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('setFetchMode')->once()->with(\PDO::FETCH_ASSOC)->andReturnTrue();
        $statement->shouldReceive('fetch')->once()->with()->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertInstanceOf(ContactPhone::class, $contactPhone);
    }

    /** @test */
    public function returnsPreviouslyMapped()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $this->em->map($e1);

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertSame($e1, $contactPhone);
    }

    /** @test */
    public function updatesOriginalData()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+41 160 21305919'
        ], $this->em, true);
        $this->em->map($e1);
        $e1->number = '+49 151 00000000';

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertFalse($contactPhone->isDirty());
    }

    /** @test */
    public function resetsData()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $this->em->map($e1);

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertFalse($contactPhone->isDirty());
        self::assertSame('+49 151 00000000', $contactPhone->number);
    }

    /** @test */
    public function resetsOnlyNonDirty()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $this->em->map($e1);
        $e1->number = '+41 160 23142312';

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertTrue($contactPhone->isDirty());
        self::assertSame('+41 160 23142312', $contactPhone->number);

        $contactPhone->reset('number');

        self::assertSame('+49 151 00000000', $contactPhone->number);
    }

    /** @test */
    public function allReturnsEmptyArray()
    {
        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->once()->andReturn(null);

        $contactPhones = $fetcher->all();

        self::assertSame([], $contactPhones);
    }

    /** @test */
    public function allReturnsArrayWithAllEntities()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], $this->em, true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], $this->em, true);

        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->times(4)->andReturn($e1, $e2, $e3, null);

        $contactPhones = $fetcher->all();

        self::assertSame([
            $e1,
            $e2,
            $e3
        ], $contactPhones);
    }

    /** @test */
    public function allReturnsRemainingEntities()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], $this->em, true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], $this->em, true);

        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->times(4)->andReturn($e1, $e2, $e3, null);

        $first = $fetcher->one();

        $contactPhones = $fetcher->all();

        self::assertSame([
            $e2,
            $e3
        ], $contactPhones);
    }

    /** @test */
    public function returnsAllItemsAfterReset()
    {
        $e1 = $this->em->map(new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true));
        $e2 = $this->em->map(new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], $this->em, true));
        $e3 = $this->em->map(new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], $this->em, true));

        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT DISTINCT t0.* FROM "contact_phone" AS t0')
            ->andReturn($statement = \Mockery::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->andReturnTrue();
        $statement->shouldReceive('fetch')->with()->times(4)
            ->andReturn($e1->getData(), $e2->getData(), $e3->getData(), false);

        $fetcher->all();

        $contactPhones = $fetcher->reset()->all();

        self::assertSame([$e1, $e2, $e3], $contactPhones);
    }

    /** @test */
    public function allReturnsLimitedAmount()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], $this->em, true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], $this->em, true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], $this->em, true);

        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->twice()->andReturn($e1, $e2, $e3, null);

        $contactPhones = $fetcher->all(2);

        self::assertSame([
            $e1,
            $e2
        ], $contactPhones);
    }

    /** @test */
    public function columnsCantBeChanged()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $fetcher->columns(['a', 'b']);

        self::assertSame('SELECT DISTINCT t0.* FROM "contact_phone" AS t0', $fetcher->getQuery());
    }

    /** @test */
    public function columnsCanBeAdded()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $fetcher->column('(a + b)', [], 'aPlusB');

        self::assertSame('SELECT DISTINCT t0.*,("t0"."a" + "t0"."b") AS aPlusB ' .
            'FROM "contact_phone" AS t0', $fetcher->getQuery());
    }

    /** @test */
    public function fetchModeCantBeChanged()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldNotReceive('query');

        $fetcher->setFetchMode(\PDO::FETCH_NUM);
    }

    public function provideJoins()
    {
        return [
            ['join', 'JOIN'],
            ['leftJoin', 'LEFT JOIN'],
            ['rightJoin', 'RIGHT JOIN'],
            ['fullJoin', 'FULL JOIN']
        ];
    }

    /** @dataProvider provideJoins
     * @test */
    public function joinsGetAliasAutomatically($method, $sql)
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        call_user_func([$fetcher, $method], StudlyCaps::class, 't0.a = t1.b');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "contact_phone" AS t0 ' . $sql . ' "studly_caps" AS t1 ON "t0"."a" = "t1"."b"',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function joinAllowsTableNames()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $fetcher->join('foo_bar', 'foo_bar.id = t0.fooBarId');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "contact_phone" AS t0 JOIN foo_bar ON foo_bar.id = "t0"."foo_bar_id"',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function translatesColumnNames()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        $fetcher->where('id', 23);

        self::assertSame('SELECT DISTINCT t0.* FROM "my_table" AS t0 WHERE "t0"."stn_id" = 23', $fetcher->getQuery());
    }

    /** @test */
    public function translatesClassNames()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        $fetcher->where(StaticTableName::class . '::id', 23);

        self::assertSame('SELECT DISTINCT t0.* FROM "my_table" AS t0 WHERE "t0"."stn_id" = 23', $fetcher->getQuery());
    }

    /** @test */
    public function throwsWhenClassIsNotJoined()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        self::expectException(NotJoined::class);
        self::expectExceptionMessage("Class " . ContactPhone::class . " not joined");

        $fetcher->where(ContactPhone::class . '::id', 23);
    }

    /** @test */
    public function doesNotTouchUnknownAlias()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        $fetcher->where('foobar.id', 23);

        self::assertSame('SELECT DISTINCT t0.* FROM "my_table" AS t0 WHERE foobar.id = 23', $fetcher->getQuery());
    }

    /** @test */
    public function knowsAliasesInParenthesis()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        $fetcher->parenthesis()->where('id = 23')->close();

        self::assertSame('SELECT DISTINCT t0.* FROM "my_table" AS t0 WHERE ("t0"."stn_id" = 23)', $fetcher->getQuery());
    }

    /** @test */
    public function doesNotReplaceUpperCaseWords()
    {
        $fetcher = $this->em->fetch(StaticTableName::class);

        $fetcher->orderBy('CASE WHEN username = email THEN 0 ELSE 1 END');

        self::assertSame(
            'SELECT DISTINCT t0.* FROM "my_table" AS t0 '
            . 'ORDER BY CASE WHEN "t0"."stn_username" = "t0"."stn_email" THEN 0 ELSE 1 END ASC',
            $fetcher->getQuery()
        );
    }

    public function provideRelations()
    {
        return [
            [
                RelationExample::class,
                'dmgd',
                '"damaged_abbrv_case" AS dmgd ON "t0"."dmgd_id" = "dmgd"."id"'
            ],
            [
                DamagedABBRVCase::class,
                'relation',
                '"relation_example" AS relation ON \("t0"."id" = "relation"."dmgd_id"\)'
            ],
            [
                RelationExample::class,
                'contactPhones',
                '"contact_phone" AS contactPhones ON \("t0"."id" = "contactPhones"."relation_id"\)'
            ],
            [
                Article::class,
                'categories',
                '"article_category" ON "t0"."id" = "article_category"."article_id" ' .
                '(LEFT )?JOIN "category" AS categories ON "article_category"."category_id" = "categories"."id"'
            ]
        ];
    }

    /** @dataProvider provideRelations
     * @test */
    public function joinRelatedCreatesJoinStatement($class, $relation, $statement)
    {
        $fetcher = $this->em->fetch($class);

        $fetcher->joinRelated($relation);

        self::assertRegExp(
            '/SELECT DISTINCT t0\.\* FROM "[A-Za-z0-9_."]+" AS t0 JOIN ' . $statement . '/',
            $fetcher->getQuery()
        );
    }

    /** @dataProvider provideRelations
     * @test */
    public function leftJoinRelatedCreatesLeftJoinStatement($class, $relation, $statement)
    {
        $fetcher = $this->em->fetch($class);

        $fetcher->leftJoinRelated($relation);

        self::assertRegExp(
            '/SELECT DISTINCT t0\.\* FROM "[A-Za-z0-9_."]+" AS t0 LEFT JOIN ' . $statement . '/',
            $fetcher->getQuery()
        );
    }

    /** @test */
    public function returnsSelf()
    {
        $fetcher = $this->em->fetch(DamagedABBRVCase::class);

        $result = $fetcher->joinRelated('relation');

        self::assertSame($fetcher, $result);
    }

    /** @test */
    public function chainedJoin()
    {
        $fetcher = $this->em->fetch(DamagedABBRVCase::class);

        $fetcher->joinRelated('relation')->joinRelated('relation.contactPhones');

        self::assertRegExp(
            '/SELECT DISTINCT t0\.\* .* JOIN "contact_phone" AS contactPhones ON ' .
            '\("relation"."id" = "contactPhones"."relation_id"\)/',
            $fetcher->getQuery()
        );
    }
}
