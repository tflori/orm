<?php

namespace ORM\Test\EntityManager;

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

    public function testExecutesStatementOnce()
    {
        $fetcher = $this->em->fetch(ContactPhone::class);

        $this->pdo->shouldReceive('query')->once()->with('SELECT t0.* FROM contact_phone AS t0')->andReturn(false);

        $fetcher->one();
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
}
