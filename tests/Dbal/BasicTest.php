<?php

namespace ORM\Test\Dbal;

use ORM\Dbal;
use ORM\Exception;
use ORM\Test\TestCase;

class BasicTest extends TestCase
{
    /** @var Dbal */
    protected $dbal;

    protected function setUp()
    {
        parent::setUp();

        $this->dbal = new Dbal\Other($this->em);
    }

    public function testEscapesIdentifiersWithDoubleQuote()
    {
        $escaped = $this->dbal->escapeIdentifier('user');

        self::assertSame('"user"', $escaped);
    }

    public function testEscapesSchemasInIdentifiers()
    {
        $escaped = $this->dbal->escapeIdentifier('user.id');

        self::assertSame('"user"."id"', $escaped);
    }

    public function testDoesNotSupportDescribe()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Not supported for this driver');

        $this->dbal->describe('any');
    }
}
