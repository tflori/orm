<?php

namespace ORM\Test\EntityManager;

use ORM\Dbal\Column;
use ORM\Dbal\Table;
use ORM\Test\TestCase;

class DescribeTest extends TestCase
{
    /** @var Table */
    protected $table;

    protected function setUp(): void
    {
        parent::setUp();

        $this->table = new Table([new Column($this->dbal, [
            'column_name' => 'id',
            'data_type' => 'int',
            'column_default' => 'sequence(AUTO_INCREMENT)',
            'is_nullable' => false
        ])]);
    }

    /** @test */
    public function callsDescribeFromDbal()
    {
        $this->dbal->shouldReceive('describe')->with('db.table')->once()->andReturn($this->table);

        $description = $this->em->describe('db.table');

        self::assertSame($this->table, $description);
    }

    /** @test */
    public function remembersPreviousCalls()
    {
        $this->dbal->shouldReceive('describe')->with('db.table')->once()->andReturn($this->table);
        $this->em->describe('db.table');

        $description = $this->em->describe('db.table');

        self::assertSame($this->table, $description);
    }
}
