<?php

namespace ORM\Test\EntityManager;

use Mockery\Mock;
use ORM\Entity;
use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\UnsupportedDriver;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class DataModificationTest extends TestCase
{
    public function provideEntitiesWithPrimary()
    {
        return [
            [new StudlyCaps(['id' => 42]), 'studly_caps', '.*id = 42'],
            [new StudlyCaps(['id' => 1]), 'studly_caps', '.*id = 1'],
            [
                new StaticTableName(['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 42]),
                'my_table',
                '.*table = \'a\' AND .*name = \'b\' AND .*bar = 42'
            ],
        ];
    }

    /**
     * @dataProvider provideEntitiesWithPrimary
     */
    public function testSyncQueriesTheDatabase($entity, $table, $whereConditions)
    {
        $this->pdo->shouldReceive('query')->once()
            ->with('/^SELECT .*\* FROM ' . $table . '.* WHERE ' . $whereConditions . '/')
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
            [['id' => 42, '"foo"' => 'bar'], 'foobar', 'INSERT INTO foobar (id,"foo") VALUES (42,\'bar\')'],
            [['foo' => 'bar'], 'foobar', 'INSERT INTO foobar (foo) VALUES (\'bar\')'],
        ];
    }

    /**
     * @dataProvider provideInsertStatements
     */
    public function testInsertStatement($data, $table, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->insert($table, $data);
    }

    /**
     * @dataProvider provideInsertStatements
     */
    public function testInsertReturnsTrue($data, $table, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()
            ->andReturn(\Mockery::mock(\PDOStatement::class));

        $result = $this->em->insert($table, $data);

        self::assertTrue($result);
    }

    public function testInsertReturnsAutoIncrementSqlite()
    {
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('sqlite');
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES/')->once()
            ->andReturn(\Mockery::mock(\PDOStatement::class));
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42');

        $result = $this->em->insert('foobar', ['foo' => 'bar'], 'default', 'id');

        self::assertSame('42', $result);
    }

    public function testInsertReturnsAutoIncrementMysql()
    {
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mysql');
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES/')->once()
            ->andReturn(\Mockery::mock(\PDOStatement::class));
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('SELECT LAST_INSERT_ID()')->once()->andReturn($statement);
        $statement->shouldReceive('fetchColumn')->once()->andReturn(42);

        $result = $this->em->insert('foobar', ['foo' => 'bar'], 'default', 'id');

        self::assertSame(42, $result);
    }

    public function testInsertReturnsAutoIncrementPgsql()
    {
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('pgsql');
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->with('/^INSERT INTO .* VALUES .* RETURNING id$/')->once()
            ->andReturn($statement);
        $statement->shouldReceive('fetchColumn')->once()->andReturn(42);

        $result = $this->em->insert('foobar', ['foo' => 'bar'], 'default', 'id');

        self::assertSame(42, $result);
    }

    public function testThrowsForUnsupportedDriverWithAutoincrement()
    {
        $this->pdo->shouldReceive('getAttribute')->with(\PDO::ATTR_DRIVER_NAME)->andReturn('foobar');

        self::expectException(UnsupportedDriver::class);
        self::expectExceptionMessage('Auto incremented column for driver foobar is not supported');

         $this->em->insert('foobar', ['foo' => 'bar'], 'default', 'id');
    }

    public function provideUpdateStatements()
    {
        return [
            [['foo' => 'bar'], ['id' => 42], 'foobar', 'UPDATE foobar SET foo = \'bar\' WHERE id = 42'],
            [['"foo"' => 'bar'], ['id' => '42'], '"foobar"', 'UPDATE "foobar" SET "foo" = \'bar\' WHERE id = \'42\''],
            [
                ['"id"' => 666, '"foo"' => 'bar'],
                ['"id"' => '42'],
                '"foobar"',
                'UPDATE "foobar" SET "id" = 666,"foo" = \'bar\' WHERE "id" = \'42\''
            ],
            [
                ['bar' => '42'],
                ['stn_table' => 'a', 'stn_name' => 'b', 'bar' => 'default'],
                'my_table',
                'UPDATE my_table SET bar = \'42\' WHERE stn_table = \'a\' AND stn_name = \'b\' AND bar = \'default\''
            ]
        ];
    }

    /**
     * @dataProvider provideUpdateStatements
     */
    public function testUpdateStatement($data, $primaryKey, $table, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andThrow(new \PDOException('Query failed'));

        self::expectException(\PDOException::class);

        $this->em->update($table, $primaryKey, $data);
    }

    /**
     * @dataProvider provideUpdateStatements
     */
    public function testUpdateReturnsSuccess($data, $primaryKey, $table, $statement)
    {
        $this->pdo->shouldReceive('query')->with($statement)->once()->andReturn(\Mockery::mock(\PDOStatement::class));

        $result = $this->em->update($table, $primaryKey, $data);

        self::assertTrue($result);
    }
}
