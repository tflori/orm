<?php

namespace ORM\Test\Dbal;

use Mockery as m;
use ORM\Test\TestCase;

class TransactionTest extends TestCase
{
    /** @test */
    public function startsATransaction()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue();

        $this->dbal->beginTransaction();
    }
    
    /** @test */
    public function commitsTheTransaction()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('commit')->with()->once()->andReturnTrue()->ordered();

        $this->dbal->beginTransaction();
        $this->dbal->commit();
    }

    /** @test */
    public function doesNotCommitIfThereIsNoTransaction()
    {
        $this->pdo->shouldNotReceive('commit');

        $this->dbal->commit();
    }

    /** @test */
    public function doesNotRollbackIfThereIsNoTransaction()
    {
        $this->pdo->shouldNotReceive('rollback');

        $this->dbal->rollback();
    }

    /** @test */
    public function createsASavepointWith2ndTransaction()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^SAVEPOINT /'))->once()->andReturnTrue()->ordered();

        $this->dbal->beginTransaction();
        $this->dbal->beginTransaction();
    }

    /** @test */
    public function committingInnerTransactionsJustReducesTheCounter()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^SAVEPOINT /'))->once()->andReturnTrue()->ordered();
        $this->pdo->shouldNotReceive('commit');

        $this->dbal->beginTransaction();
        $this->dbal->beginTransaction();
        $this->dbal->commit();
    }

    /** @test */
    public function committingAllResetsTheCounterAndCommits()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^SAVEPOINT /'))->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('commit')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();

        $this->dbal->beginTransaction();
        $this->dbal->beginTransaction();
        $this->dbal->commit(true);
        $this->dbal->beginTransaction();
    }

    /** @test */
    public function rollbackOnly2ndTransaction()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^SAVEPOINT /'))->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^ROLLBACK TO /'))->once()->andReturnTrue()->ordered();

        $this->dbal->beginTransaction();
        $this->dbal->beginTransaction();
        $this->dbal->rollback();
    }

    /** @test */
    public function rollbackBothTransactions()
    {
        $this->pdo->shouldReceive('beginTransaction')->with()->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^SAVEPOINT /'))->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('exec')->with(m::pattern('/^ROLLBACK TO /'))->once()->andReturnTrue()->ordered();
        $this->pdo->shouldReceive('rollback')->with()->once()->andReturnTrue()->ordered();

        $this->dbal->beginTransaction();
        $this->dbal->beginTransaction();
        $this->dbal->rollback();
        $this->dbal->rollback();
    }
}
