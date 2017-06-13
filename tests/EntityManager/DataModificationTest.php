<?php

namespace ORM\Test\EntityManager;

use Mockery\Mock;
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
            [new StudlyCaps(['id' => 42]), 'studly_caps', '.*"id" = 42'],
            [new StudlyCaps(['id' => 1]), 'studly_caps', '.*"id" = 1'],
            [
                new StaticTableName(['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 42]),
                'my_table',
                '.*table" = \'a\' AND .*name" = \'b\' AND .*bar" = 42'
            ],
        ];
    }

    /**
     * @dataProvider provideEntitiesWithPrimary
     */
    public function testSyncQueriesTheDatabase($entity, $table, $whereConditions)
    {
        $this->pdo->shouldReceive('query')->once()
            ->with('/^SELECT .*\* FROM "' . $table . '".* WHERE ' . $whereConditions . '/')
            ->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);
        $this->em->sync($entity);
    }

    public function provideEntitiesWithIncompletePrimary()
    {
        return [
            [new StudlyCaps(), 'id'],
            [new StaticTableName(), 'table'],
            [new StaticTableName(['stn_table' => 'a']), 'name'],
        ];
    }

    /**
     * @dataProvider provideEntitiesWithIncompletePrimary
     */
    public function testSyncThrowsWithIncompletePrimary($entity, $message)
    {
        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Incomplete primary key - missing ' . $message);

        $this->em->sync($entity);
    }

    public function provideChangedEntities()
    {
        return [
            [new StudlyCaps(['id' => 42]), ['id' => 42, 'foo' => 'bar']],
            [
                new StaticTableName([
                    'stn_table' => 'a', 'stn_name' => 'b'
                ]),
                [
                    'stn_table' => 'a', 'stn_name' => 'b', 'bar' => 'default', 'col' => 'foobar'
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideChangedEntities
     */
    public function testSyncUpdatesOriginalData($entity, $newData)
    {
        /** @var Mock|Entity $entity */
        $entity = \Mockery::mock($entity);

        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/^SELECT .* FROM .* WHERE/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn($newData);

        $entity->shouldReceive('setOriginalData')->once()->with($newData);

        $this->em->sync($entity);
    }

    public function testDoesNotChangeTheData()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/^SELECT .* FROM .* WHERE/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->em->sync($entity);

        self::assertSame('baz', $entity->foo);
        self::assertTrue($entity->isDirty('foo'));
    }

    public function testDoesChangeTheData()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/^SELECT .* FROM .* WHERE/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->em->sync($entity, true);

        self::assertSame('bar', $entity->foo);
        self::assertFalse($entity->isDirty('foo'));
    }

    public function testMapsTheEntity()
    {
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with('/^SELECT .* FROM .* WHERE/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')
                  ->andReturn(false);

        $this->em->map(new StudlyCaps(['id' => 42]));
        $studlyCaps = new StudlyCaps(['id' => 42]);

        $this->em->sync($studlyCaps);

        self::assertSame($studlyCaps, $this->em->fetch(StudlyCaps::class, 42));
    }

    public function testReturnsTrueWhenEntityPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with('/^SELECT .* FROM .* WHERE/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')
                  ->andReturn(['id' => 42, 'foo' => 'bar']);

        $result = $this->em->sync($entity, true);

        self::assertTrue($result);
    }

    public function testReturnsFalseWhenEntityNotPersisted()
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'baz']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')
                  ->with('/^SELECT .* FROM .* WHERE/')
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
                new StudlyCaps(['id' => 42, 'foo' => 'bar']),
                'INSERT INTO "studly_caps" ("id","foo") VALUES (42,\'bar\')'],
            [
                new StudlyCaps(['foo' => 'bar']),
                'INSERT INTO "studly_caps" ("foo") VALUES (\'bar\')'
            ],
        ];
    }

    /**
     * @dataProvider provideInsertStatements
     */
    public function testInsertStatement($entity, $statement)
    {
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->atLeast(1)->andReturn('mysql');
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->insert($entity);
    }

    public function testInsertReturnsTrue()
    {
        $entity = new Psr0_StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->pdo->shouldReceive('query')
            ->with('INSERT INTO "psr0_studly_caps" ("id","foo") VALUES (42,\'bar\')')
            ->once()->andReturn(\Mockery::mock(\PDOStatement::class));
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

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

    /**
     * @dataProvider provideDrivers
     */
    public function testDoesNotUseAutoIncrement($driver)
    {
        $entity = new StudlyCaps(['id' => 42, 'foo' => 'bar']);

        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->atLeast()->once()
            ->andReturn($driver);
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES/')->once()
                  ->andReturn(\Mockery::mock(\PDOStatement::class));

        $result = $this->em->insert($entity, false);

        self::assertTrue($result);
    }

    public function testInsertReturnsAutoIncrementSqlite()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('sqlite');
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES/')->once()
            ->andReturn(\Mockery::mock(\PDOStatement::class));
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42');
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame('42', $entity->id);
    }

    public function testInsertReturnsAutoIncrementMysql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mysql');
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES/')->once()
            ->andReturn(\Mockery::mock(\PDOStatement::class));
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('SELECT LAST_INSERT_ID()')->once()->andReturn($statement);
        $statement->shouldReceive('fetchColumn')->once()->andReturn(42);
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame(42, $entity->id);
    }

    public function testInsertReturnsAutoIncrementPgsql()
    {
        $entity = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('getDbal')->passthru();
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('pgsql');
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES .* RETURNING id$/')->once()
            ->andReturn($statement);
        $statement->shouldReceive('fetchColumn')->once()->andReturn(42);
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $result = $this->em->insert($entity);

        self::assertSame(true, $result);
        self::assertSame(42, $entity->id);
    }

    public function testThrowsForUnsupportedDriverWithAutoincrement()
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
            [new StudlyCaps(['id' => 42, 'foo' => 'bar']), 'UPDATE "studly_caps" SET "foo" = \'bar\' WHERE "id" = 42'],
            [
                new StudlyCaps(['id' => '42', 'foo' => 'bar']),
                'UPDATE "studly_caps" SET "foo" = \'bar\' WHERE "id" = \'42\''
            ],
            [
                new StaticTableName(['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 'default', 'stn_col1' => 'abc']),
                'UPDATE "my_table" SET "stn_col1" = \'abc\''
                . ' WHERE "stn_table" = \'a\' AND "stn_name" = \'b\' AND "bar" = \'default\''
            ]
        ];
    }

    /**
     * @dataProvider provideUpdateStatements
     */
    public function testUpdateStatement($entity, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->update($entity);
    }

    /**
     * @dataProvider provideUpdateStatements
     */
    public function testUpdateReturnsSuccessAndSyncs($entity, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andReturn(\Mockery::mock(\PDOStatement::class));
        $this->em->shouldReceive('sync')->with($entity, true)->once()->andReturn(true);

        $result = $this->em->update($entity);

        self::assertTrue($result);
    }

    public function provideDeleteStatements()
    {
        return [
            [new StudlyCaps(['id' => 42, 'foo' => 'bar']), 'DELETE FROM "studly_caps" WHERE "id" = 42'],
            [new StudlyCaps(['id' => '42']), 'DELETE FROM "studly_caps" WHERE "id" = \'42\'']
        ];
    }

    /**
     * @dataProvider provideDeleteStatements
     */
    public function testDeleteStatement($entity, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->delete($entity);
    }

    /**
     * @dataProvider provideDeleteStatements
     */
    public function testDeleteReturnsSuccess($entity, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $result = $this->em->delete($entity);

        self::assertTrue($result);
    }

    /**
     * @dataProvider provideDeleteStatements
     */
    public function testDeleteRemovesOriginalData(Entity $entity, $statement)
    {
        $entity->setOriginalData($entity->getData());
        self::assertFalse($entity->isDirty());

        $this->pdo->shouldReceive('query')->with($statement)->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $this->em->delete($entity);

        self::assertTrue($entity->isDirty());
    }
}
