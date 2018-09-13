<?php

namespace ORM\Test\Dbal;

use Mockery as m;
use ORM\Dbal\Mysql;
use ORM\Dbal\Other;
use ORM\Dbal\Pgsql;
use ORM\Dbal\Sqlite;
use ORM\Exception\InvalidArgument;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\Entity\Examples\ContactPhone;
use ORM\Test\TestCase;

class BulkInsertTest extends TestCase
{
    public function provideDrivers()
    {
        return [
            [Mysql::class],
            [Pgsql::class],
            [Sqlite::class],
            [Other::class],
        ];
    }

    /** @dataProvider provideDrivers
     * @param $driver
     * @test */
    public function insertReturnsFalseWithoutEntities($driver)
    {
        $dbal = new $driver($this->em);

        self::assertFalse($dbal->insert());
        self::assertFalse($dbal->insertAndSync());
        self::assertFalse($dbal->insertAndSyncWithAutoInc());
    }

    /** @dataProvider provideDrivers
     * @param $driver
     * @test */
    public function insertThrowsWhenTypesDiffer($driver)
    {
        $dbal = new $driver($this->em);

        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('$entities[1] is not from the same type');

        self::assertFalse($dbal->insert(new Article, new Category));
        self::assertFalse($dbal->insertAndSync(new Article, new Category));
        self::assertFalse($dbal->insertAndSyncWithAutoInc(new Article, new Category));
    }

    /** @test */
    public function bulkInsertWithoutSync()
    {
        $articles = [
            new Article(['id' => 23, 'text' => 'foo']),
            new Article(['id' => 42])
        ];

        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/INSERT INTO .* \("id","text"\) VALUES \(23,\'foo\'\),\(42,NULL\)/'
        ))->once()->andReturn(m::mock(\PDOStatement::class));

        $this->dbal->insert(...$articles);
    }

    /** @test */
    public function bulkInsertWithCompositePrimaryKey()
    {
        $contacts = [
            new ContactPhone(['id' => 23, 'name' => 'mobile', 'number' => '+1 555 2323']),
            new ContactPhone(['id' => 23, 'name' => 'business', 'number' => '+1 555 2424']),
        ];

        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/INSERT INTO .* \("id","name","number"\) VALUES \(.*\),\(.*\)/'
        ))->once()->andReturn(m::mock(\PDOStatement::class));
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE \("id","name"\) IN \(.*\)/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('fetch')->with(\PDO::FETCH_ASSOC)
            ->times(3)->andReturn(
                ['id' => 23, 'name' => 'business', 'number' => '+1 555 2424', 'created' => date('c')],
                ['id' => 23, 'name' => 'mobile', 'number' => '+1 555 2323', 'created' => date('c')],
                false
            );

        $this->dbal->insertAndSync(...$contacts);

        self::assertSame('+1 555 2323', $contacts[0]->number);
        self::assertNotNull($contacts[0]->created);
        self::assertSame('+1 555 2424', $contacts[1]->number);
        self::assertNotNull($contacts[1]->created);
    }
}
