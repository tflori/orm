<?php

namespace ORM\Test\EntityManager;

use Mockery\Mock;
use ORM\EntityFetcher;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\TestCase;

class EntityFetcherTest extends TestCase
{
    public function testRunsQueryWithoutParameters()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->once()->with('SELECT t0.* FROM contact_phone AS t0');

        $fetcher->one();
    }

    public function testReturnsNullWhenQueryFails()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->andReturn(false);

        $result = $fetcher->one();

        self::assertNull($result);
    }

    public function testReturnsNullWhenResultIsEmpty()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn(false);

        $result = $fetcher->one();

        self::assertNull($result);
    }

    public function testExecutesQueryOnce()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT t0.* FROM contact_phone AS t0')
            ->andReturn(false);

        $fetcher->one();
        $fetcher->one();
    }

    public function testUsesSpecifiedQuery()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
            ->with('SELECT * FROM contact_phone WHERE id = 42 AND name = \'mobile\'')
            ->andReturn(false);

        $fetcher->setQuery('SELECT * FROM contact_phone WHERE id = 42 AND name = \'mobile\'');
        $fetcher->one();
    }

    public function testReplacesQuestionmarksWithQuotedValue()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('SELECT * FROM contact_phone WHERE id = 42 AND name = \'mobile\'')
                  ->andReturn(false);
        $this->pdo->shouldReceive('quote')->once()->with(42)->andReturn('42');
        $this->pdo->shouldReceive('quote')->once()->with('mobile')->andReturn('\'mobile\'');

        $fetcher->setQuery('SELECT * FROM contact_phone WHERE id = ? AND name = ?', [42, 'mobile']);
        $fetcher->one();
    }

    public function testReturnsAnEntity()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->with(\PDO::FETCH_ASSOC)->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertInstanceOf(ContactPhone::class, $contactPhone);
    }

    public function testReturnsPreviouslyMapped()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $this->em->map($e1);

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertSame($e1, $contactPhone);
    }

    public function testUpdatesOriginalData()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+41 160 21305919'
        ], true);
        $this->em->map($e1);
        $e1->number = '+49 151 00000000';

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertFalse($contactPhone->isDirty());
    }

    public function testResetsData()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $this->em->map($e1);

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'name' => 'mobile',
            'number' => '+49 151 00000000'
        ]);

        $contactPhone = $fetcher->one();

        self::assertFalse($contactPhone->isDirty());
        self::assertSame('+49 151 00000000', $contactPhone->number);
    }

    public function testResetsOnlyNonDirty()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $this->em->map($e1);
        $e1->number = '+41 160 23142312';

        $fetcher = $this->em->fetch(ContactPhone::class);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->andReturn($statement);
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

    public function testAllReturnsEmptyArray()
    {
        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->once()->andReturn(null);

        $contactPhones = $fetcher->all();

        self::assertSame([], $contactPhones);
    }

    public function testAllReturnsArrayWithAllEntities()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], true);

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

    public function testAllReturnsRemainingEntities()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], true);

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

    public function testAllReturnsLimitedAmount()
    {
        $e1 = new ContactPhone([
            'id' => 42,
            'name' => 'mobile'
        ], true);
        $e2 = new ContactPhone([
            'id' => 43,
            'name' => 'mobile'
        ], true);
        $e3 = new ContactPhone([
            'id' => 44,
            'name' => 'mobile'
        ], true);

        /** @var EntityFetcher|Mock $fetcher */
        $fetcher = \Mockery::mock(EntityFetcher::class, [$this->em, ContactPhone::class])->makePartial();
        $fetcher->shouldReceive('one')->twice()->andReturn($e1, $e2, $e3, null);

        $contactPhones = $fetcher->all(2);

        self::assertSame([
            $e1,
            $e2
        ], $contactPhones);
    }
}
