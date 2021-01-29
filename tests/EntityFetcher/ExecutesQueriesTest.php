<?php

namespace ORM\Test\EntityFetcher;

use Mockery as m;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class ExecutesQueriesTest extends TestCase
{
    /** @test */
    public function updateTranslatesColumnNames()
    {
        $fetcher = $this->em->fetch(Article::class)->where('someColumn', 42);

        $this->pdo->shouldReceive('query')
            ->with('UPDATE "article" AS t0 SET "any_column" = \'foo bar\' WHERE "t0"."some_column" = 42')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->once()->andReturn(3);

        $fetcher->update(['anyColumn' => 'foo bar']);
    }

    /** @test */
    public function insertTranslatesColumnNames()
    {
        $fetcher = $this->em->fetch(Article::class);

        $this->pdo->shouldReceive('query')
            ->with('INSERT INTO "article" ("first_column","second_column") VALUES (\'foo bar\',NULL),(NULL,42)')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->once()->andReturn(1);

        $fetcher->insert(['firstColumn' => 'foo bar'], ['secondColumn' => 42]);
    }
}
