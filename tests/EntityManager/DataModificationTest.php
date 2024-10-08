<?php

namespace ORM\Test\EntityManager;

use Mockery\Mock;
use Mockery as m;
use ORM\Dbal\Mysql;
use ORM\Dbal\Sqlite;
use ORM\Entity;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\UnsupportedDriver;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class DataModificationTest extends TestCase
{
    public function provideEntitiesWithPrimary()
    {
        return [
            [StudlyCaps::class, ['id' => 42], 'studly_caps', '.*"id" = 42'],
            [StudlyCaps::class, ['id' => 1], 'studly_caps', '.*"id" = 1'],
            [
                StaticTableName::class,
                ['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 42],
                'my_table',
                '.*table" = \'a\' AND .*name" = \'b\' AND .*bar" = 42'
            ],
        ];
    }

    /** @dataProvider provideEntitiesWithPrimary
     * @test */
    public function syncQueriesTheDatabase($class, $data, $table, $whereConditions)
    {
        $entity = new $class($data);
        $this->pdo->shouldReceive('query')->once()
            ->with(m::pattern('/^SELECT .*\* FROM "' . $table . '".* WHERE ' . $whereConditions . '/'))
            ->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);
        $this->em->sync($entity);
    }

    public function provideEntitiesWithIncompletePrimary()
    {
        return [
            [StudlyCaps::class, [], 'id'],
            [StaticTableName::class, [], 'table'],
            [StaticTableName::class, ['stn_table' => 'a'], 'name'],
        ];
    }

    /** @dataProvider provideEntitiesWithIncompletePrimary
     * @test */
    public function syncThrowsWithIncompletePrimary($class, $data, $message)
    {
        $entity = new $class($data);

        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Incomplete primary key - missing ' . $message);

        $this->em->sync($entity);
    }

    public function provideChangedEntities()
    {
        return [
            [StudlyCaps::class, ['id' => 42], ['id' => 42, 'foo' => 'bar']],
            [
                StaticTableName::class,
                ['stn_table' => 'a', 'stn_name' => 'b'],
                [
                    'stn_table' => 'a',
                    'stn_name' => 'b',
                    'bar' => 'default',
                    'col' => 'foobar',
                ]
            ],
        ];
    }

    /** @dataProvider provideChangedEntities
     * @test */
    public function syncUpdatesOriginalData($class, $data, $newData)
    {
        /** @var Mock|Entity $entity */
        $entity = m::mock(new $class($data));

        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn($newData);

        $entity->shouldReceive('setOriginalData')->once()->with($newData);

        $this->em->sync($entity);
    }

    /** @test */
    public function doesNotChangeTheData()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->em->sync($entity);

        self::assertSame('baz', $entity->foo);
        self::assertTrue($entity->isDirty('foo'));
    }

    /** @test */
    public function doesChangeTheData()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->em->sync($entity, true);

        self::assertSame('bar', $entity->foo);
        self::assertFalse($entity->isDirty('foo'));
    }

    /** @test */
    public function mapsTheEntity()
    {
        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')
                  ->andReturn(false);

        $this->em->map(new StudlyCaps(['id' => 42]));
        $studlyCaps = new StudlyCaps(['id' => 42]);

        $this->em->sync($studlyCaps);

        self::assertSame($studlyCaps, $this->em->fetch(StudlyCaps::class, 42));
    }

    /** @test */
    public function returnsTrueWhenEntityPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $result = $this->em->sync($entity, true);

        self::assertTrue($result);
    }

    /** @test */
    public function returnsFalseWhenEntityNotPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = m::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with(m::pattern('/^SELECT .* FROM .* WHERE/'))
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')
                  ->andReturn(false);

        $result = $this->em->sync($entity, true);

        self::assertFalse($result);
    }

    public function provideInsertStatements()
    {
        return [
            [
                StudlyCaps::class,
                ['id' => 42, 'foo' => 'bar'],
                'INSERT INTO "studly_caps" ("id","foo") VALUES (42,\'bar\')'
            ],
            [
                StudlyCaps::class,
                ['foo' => 'bar'],
                'INSERT INTO "studly_caps" ("foo") VALUES (\'bar\')'
            ],
        ];
    }

    /** @dataProvider provideInsertStatements
     * @test */
    public function insertStatement($class, $data, $statement)
    {
        $entity = new $class($data);
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mysql');
        $this->dbal->shouldReceive('beginTransaction')->with()->once();
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));
        $this->dbal->shouldReceive('rollback')->with()->once();

        self::expectException(\PDOException::class);

        $this->em->insert($entity);
    }

    /** @test */
    public function insertReturnsTrue()
    {
        $entity = new Psr0_StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->pdo->shouldReceive('query')
            ->with('INSERT INTO "psr0_studly_caps" ("id","foo") VALUES (42,\'bar\')')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);
        $this->pdo->shouldReceive('query')->with('SELECT * FROM "psr0_studly_caps" WHERE "id" IN (42)')
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->once()->with(\PDO::FETCH_ASSOC)->andReturnTrue();
        $statement->shouldReceive('fetch')->with()
            ->twice()->andReturn(['id' => 42, 'foo' => 'bar'], false);

        $result = $this->em->insert($entity);

        self::assertTrue($result);
    }

    public function provideDrivers()
    {
        return [
            ['mysql'],
            ['pgsql'],
            ['sqlite'],
            ['mssql']
        ];
    }

    /** @dataProvider provideDrivers
     * @test */
    public function doesNotUseAutoIncrement($driver)
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)
            ->atLeast()->once()->andReturn($driver);
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);
        $this->pdo->shouldReceive('query')->with(m::pattern('/^SELECT \* FROM .* WHERE .* IN (.*)/'))
            ->once()->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('setFetchMode')->once()->with(\PDO::FETCH_ASSOC)->andReturnTrue();
        $statement->shouldReceive('fetch')->with()
            ->twice()->andReturn(['id' => 42, 'foo' => 'bar'], false);

        $result = $this->em->insert($entity, false);

        self::assertTrue($result);
    }

    /** @test */
    public function insertUpdatesAutoIncrementSqlite()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Sqlite::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andReturn(m::mock(\PDOStatement::class))->ordered();
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42')->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE "id" <= 42 ORDER BY "id" DESC LIMIT 1/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)
            ->once()->andReturn([['id' => '42', 'foo' => 'bar']])->ordered();
        $dbal->shouldReceive('commit')->with()->once()->ordered();

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame('42', $entity->id);
    }

    /** @test */
    public function insertWithAutoIncrementUsesTransactionsSqlite()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Sqlite::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andReturn(m::mock(\PDOStatement::class))->ordered();
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42')->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE "id" <= 42 ORDER BY "id" DESC LIMIT 1/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)
            ->once()->andReturn([['id' => '42', 'foo' => 'bar']])->ordered();
        $dbal->shouldReceive('commit')->with()->once()->ordered();

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame('42', $entity->id);
    }

    /** @test */
    public function insertWithAutoIncrementRollbackTransactionSqlite()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Sqlite::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andThrow(new \PDOException('Query failed'))->ordered();
        $dbal->shouldReceive('rollback')->with()->once()->ordered();

        self::expectException(\PDOException::class);

        $this->em->insert($entity);
    }

    /** @test */
    public function insertReturnsAutoIncrementMysql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Mysql::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andReturn(m::mock(\PDOStatement::class))->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE "id" >= LAST_INSERT_ID()/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)
            ->once()->andReturn([['id' => 42, 'foo' => 'bar']])->ordered();
        $dbal->shouldReceive('commit')->with()->once()->ordered();

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame(42, $entity->id);
    }

    /** @test */
    public function insertWithAutoIncrementUsesTransactionsMysql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Mysql::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andReturn(m::mock(\PDOStatement::class))->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE "id" >= LAST_INSERT_ID()/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('fetchAll')->with(\PDO::FETCH_ASSOC)
            ->once()->andReturn([['id' => 42, 'foo' => 'bar']])->ordered();
        $dbal->shouldReceive('commit')->with()->once()->ordered();

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame(42, $entity->id);
    }

    /** @test */
    public function insertWithAutoIncrementRollbackTransactionMysql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')
            ->andReturn($dbal = m::mock(Mysql::class, [$this->em])->makePartial());

        $dbal->shouldReceive('beginTransaction')->with()->once()->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES/'))
            ->once()->andThrow(new \PDOException('Query failed'))->ordered();
        $dbal->shouldReceive('rollback')->with()->once()->ordered();

        self::expectException(\PDOException::class);

        $this->em->insert($entity);
    }

    /** @test */
    public function insertReturnsAutoIncrementPgsql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('pgsql');

        $this->pdo->shouldReceive('query')->with(m::pattern('/^INSERT INTO .* VALUES .* RETURNING id$/'))
            ->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('fetchColumn')->with()
            ->twice()->andReturn(42, false)->ordered();
        $this->pdo->shouldReceive('query')->with(m::pattern(
            '/SELECT \* FROM .* WHERE "id" IN \(42\)/'
        ))->once()->andReturn($statement = m::mock(\PDOStatement::class))->ordered();
        $statement->shouldReceive('setFetchMode')->once()->with(\PDO::FETCH_ASSOC)->andReturnTrue();
        $statement->shouldReceive('fetch')->with()
            ->twice()->andReturn(['id' => 42, 'foo' => 'bar'], false)->ordered();

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame(42, $entity->id);
    }

    /** @test */
    public function throwsForUnsupportedDriverWithAutoincrement()
    {
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('foobar');

        self::expectException(UnsupportedDriver::class);
        self::expectExceptionMessage('Auto incremented column for this driver is not supported');

        $this->em->insert(new StudlyCaps(['foo' => 'bar']));
    }

    public function provideUpdateStatements()
    {
        return [
            [
                StudlyCaps::class,
                ['id' => 42, 'foo' => 'bar'],
                'UPDATE "studly_caps" SET "foo" = \'bar\' WHERE "id" = 42'
            ],
            [
                StudlyCaps::class,
                ['id' => '42', 'foo' => 'bar'],
                'UPDATE "studly_caps" SET "foo" = \'bar\' WHERE "id" = \'42\''
            ],
            [
                StaticTableName::class,
                ['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 'default', 'stn_col1' => 'abc'],
                'UPDATE "my_table" SET "stn_col1" = \'abc\''
                . ' WHERE "stn_table" = \'a\' AND "stn_name" = \'b\' AND "bar" = \'default\''
            ]
        ];
    }

    /** @dataProvider provideUpdateStatements
     * @test */
    public function updateStatement($class, $data, $statement)
    {
        $entity = new $class($data);
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->update($entity);
    }

    /** @dataProvider provideUpdateStatements
     * @test */
    public function updateReturnsSuccessAndSyncs($class, $data, $query)
    {
        $entity = new $class($data);
        $statement = m::mock(\PDOStatement::class);
        $statement->shouldReceive('rowCount')->andReturn(1);
        $this->pdo->shouldReceive('query')->with($query)->once()->andReturn($statement);
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $result = $this->em->update($entity);

        self::assertTrue($result);
    }

    public function provideDeleteStatements()
    {
        return [
            [StudlyCaps::class, ['id' => 42, 'foo' => 'bar'], 'DELETE FROM "studly_caps" WHERE "id" = 42'],
            [StudlyCaps::class, ['id' => '42'], 'DELETE FROM "studly_caps" WHERE "id" = \'42\'']
        ];
    }

    /** @dataProvider provideDeleteStatements
     * @test */
    public function deleteStatement($class, $data, $statement)
    {
        $entity = new $class($data);
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $entity->delete();
    }

    /** @dataProvider provideDeleteStatements
     * @test */
    public function deleteReturnsSuccess($class, $data, $statement)
    {
        $entity = new $class($data);
        $this->pdo->shouldReceive('query')->with($statement)->once()
            ->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $result = $this->em->delete($entity);

        self::assertTrue($result);
    }

    /** @dataProvider provideDeleteStatements
     * @test */
    public function deleteRemovesOriginalData($class, $data, $statement)
    {
        $entity = new $class($data);
        $entity->setOriginalData($entity->getData());
        self::assertFalse($entity->isDirty());

        $this->pdo->shouldReceive('query')->with($statement)->once()
            ->andReturn($statement = m::mock(\PDOStatement::class));
        $statement->shouldReceive('rowCount')->andReturn(1);

        $entity->delete();

        self::assertTrue($entity->isDirty());
        self::assertFalse($entity->exists());
    }
}
