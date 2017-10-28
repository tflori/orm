<?php

namespace ORM\Test\Dbal;

use ORM\Dbal;
use ORM\Exception;
use ORM\Test\TestCase;

class BasicTest extends TestCase
{
    /** @var Dbal\Dbal */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Dbal\Other($this->em);
    }

    /** @test */
    public function escapesIdentifiersWithDoubleQuote()
    {
        $escaped = $this->dbal->escapeIdentifier('user');

        self::assertSame('"user"', $escaped);
    }

    /** @test */
    public function escapesSchemasInIdentifiers()
    {
        $escaped = $this->dbal->escapeIdentifier('user.id');

        self::assertSame('"user"."id"', $escaped);
    }

    /** @test */
    public function doesNotSupportDescribe()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Not supported for this driver');

        $this->dbal->describe('any');
    }
}
