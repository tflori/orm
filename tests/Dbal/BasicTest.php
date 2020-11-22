<?php

namespace ORM\Test\Dbal;

use ORM\Dbal;
use ORM\EM;
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
    public function doesNotEscapeExpressions()
    {
        $escaped = $this->dbal->escapeIdentifier(EM::raw('DATE("column")'));

        self::assertSame('DATE("column")', $escaped);
    }

    /** @test */
    public function doesNotSupportDescribe()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Describe is not supported by this driver');

        $this->dbal->describe('any');
    }



    /** @test */
    public function doesNotSupportUpdateWithJoins()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Updates with joins are not supported by this driver');

        $this->em->query('examples')
            ->join('names', 'exampleId = examples.id')
            ->where('examples.id', 42)
            ->update(['foo' => EM::raw('names.foo')]);
    }
}
