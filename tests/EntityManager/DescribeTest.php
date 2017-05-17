<?php

namespace ORM\Test\EntityManager;

use ORM\Dbal\Column;
use ORM\Dbal\Type\Integer;
use ORM\Test\TestCase;

class DescribeTest extends TestCase
{
    public function testCallsDescribeFromDbal()
    {
        $column = new Column('id', new Integer(), true, false);
        $this->dbal->shouldReceive('describe')->with('db.table')->once()->andReturn([$column]);

        $description = $this->em->describe('db.table');

        self::assertSame([$column], $description);
    }

    public function testRemembersPreviousCalls()
    {
        $column = new Column('id', new Integer(), true, false);
        $this->dbal->shouldReceive('describe')->with('db.table')->once()->andReturn([$column]);
        $this->em->describe('db.table');

        $description = $this->em->describe('db.table');

        self::assertSame([$column], $description);
    }
}
