<?php

namespace ORM\Test\Entity;

use Mockery\Mock;
use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\UnsupportedDriver;
use ORM\Test\Entity\Examples\Psr0_StudlyCaps;
use ORM\Test\Entity\Examples\Snake_Ucfirst;
use ORM\Test\Entity\Examples\StaticTableName;
use ORM\Test\Entity\Examples\StudlyCaps;
use ORM\Test\TestCase;

class SaveEntityTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

    }

    public function testSavesNewEntity()
    {
        $this->em->shouldReceive('sync')->andReturn(false)->byDefault();

        /** @var StudlyCaps $studlyCaps */
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        $this->em->shouldReceive('insert')->with('studly_caps', ['foo' => 'bar'], 'default', 'id')->once()
            ->andReturn(42);

        $studlyCaps->save($this->em);
    }

    public function testSavesInSpecifiedConnection()
    {
        $this->em->shouldReceive('sync')->andReturn(false)->byDefault();

        $snake_ucfirst = new Snake_Ucfirst(['foo' => 'bar']);
        $this->em->shouldReceive('insert')->with('snake_ucfirst', ['foo' => 'bar'], 'dw', 'my_key')->once()
            ->andReturn(42);

        $snake_ucfirst->save($this->em);
    }

    public function testThrowsWithoutPrimaryAndAutoincrement()
    {
        $staticTableName = new Psr0_StudlyCaps(['foo' => 'bar']);
        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Primary key consist of [id] nothing given');

        $staticTableName->save($this->em);
    }

    public function testThrowsWithoutPrimaryAndMultipleKeys()
    {
        $staticTableName = new StaticTableName(['stn_table' => 'foobar']);
        self::expectException(IncompletePrimaryKey::class);
        self::expectExceptionMessage('Primary key consist of [table,name,foo] only stn_table,bar given');

        $staticTableName->save($this->em);
    }

    public function testRequestsActualData()
    {
        $studlyCaps = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/^SELECT .* FROM studly_caps .* WHERE .*id = 42/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn(['id' => 42, 'foo' => 'bar']);
        $this->pdo->shouldNotReceive('query')->with('/^UPDATE studly_caps/');

        $studlyCaps->save($this->em);
    }

    public function testUpdatesIfDirty()
    {
        $studlyCaps = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->atLeast()->once()
                  ->with('/^SELECT .* FROM studly_caps/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn(['id' => 42, 'foo' => 'baz']);
        $this->pdo->shouldReceive('query')->once()->with('UPDATE studly_caps SET foo = \'bar\' WHERE id = 42');

        $studlyCaps->save($this->em);
    }

    public function testInsertsIfNotPersisted()
    {
        $studlyCaps = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->atLeast()->once()
                  ->with('/^SELECT .* FROM studly_caps/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn(false);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo,id) VALUES (\'bar\',42)')
                  ->andReturn(false);

        $studlyCaps->save($this->em);
    }

    public function testGetsLastInsertIdByLastInsertId()
    {
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo) VALUES (\'bar\')')
                  ->andReturn($statement);
        $this->pdo->shouldReceive('query')->with('/^SELECT .* FROM/')->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn('sqlite');
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42');

        $studlyCaps->save($this->em);
    }

    public function testGetsLastInsertIdByQuery()
    {
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo) VALUES (\'bar\')')
                  ->andReturn($statement);
        $this->pdo->shouldReceive('query')->with('/^SELECT .* FROM/')->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->andReturn(['id' => 42, 'foo' => 'bar']);

        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn('mysql');
        $this->pdo->shouldReceive('query')->once()->with('SELECT LAST_INSERT_ID()')->andReturn($statement);
        $statement->shouldReceive('fetchColumn')->andReturn(42);

        $studlyCaps->save($this->em);
    }

    public function testGetsLastInsertIdByReturning()
    {
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo) VALUES (\'bar\') RETURNING id')
                  ->andReturn($statement);
        $this->pdo->shouldReceive('query')->with('/^SELECT .* FROM/')->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->andReturn(['id' => 42, 'foo' => 'bar']);

        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn('pgsql');
        $statement->shouldReceive('fetchColumn')->andReturn(42);

        $studlyCaps->save($this->em);
    }

    public function provideSupportedDriver()
    {
        return [
            ['sqlite'],
            ['mysql'],
            ['pgsql']
        ];
    }

    /**
     * @dataProvider provideSupportedDriver
     */
    public function testThrowsWhenInsertFails($driver)
    {
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/^INSERT INTO .* VALUES /')
                  ->andThrow(\PDOException::class, 'Query failed');
        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn($driver);

        self::expectException(\PDOException::class);
        self::expectExceptionMessage('Query failed');

        $studlyCaps->save($this->em);
    }

    public function testThrowsForUnsupportedDriver()
    {
        $driver = 'foobar';
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        $this->pdo->shouldReceive('getAttribute')->once()->with(\PDO::ATTR_DRIVER_NAME)->andReturn($driver);

        self::expectException(UnsupportedDriver::class);
        self::expectExceptionMessage('Auto incremented column for driver ' . $driver . ' is not supported');

        $studlyCaps->save($this->em);
    }

    public function testFetchesThewNewEntity()
    {
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo) VALUES (\'bar\')')
                  ->andReturn($statement);
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42');
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/SELECT .* FROM studly_caps .* WHERE .*id = \'42\'/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->andReturn(['id' => 42, 'foo' => 'bar']);

        $studlyCaps->save($this->em);

        self::assertSame(42, $studlyCaps->id);
    }

    public function testMapsTheEntity()
    {
        /** @var StudlyCaps $studlyCaps */
        $studlyCaps = new StudlyCaps(['foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo) VALUES (\'bar\')')
                  ->andReturn($statement);
        $this->pdo->shouldReceive('lastInsertId')->once()->andReturn('42');
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
                  ->with('/SELECT .* FROM studly_caps .* WHERE .*id = \'42\'/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->once()->andReturn(['id' => '42', 'foo' => 'bar']);

        $studlyCaps->save($this->em);
        $mapped = $this->em->fetch(StudlyCaps::class, $studlyCaps->id);

        self::assertSame($mapped, $studlyCaps);
    }

    public function testUpdatesTheEntity()
    {
        $studlyCaps = new StudlyCaps([
            'id' => 42,
            'foo' => 'bar',
            'created' => '2016-12-23T07:30:30Z',
            'updated' => '2016-12-23T07:30:30Z',
        ]);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->twice()
                  ->with('/^SELECT .* FROM studly_caps/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn([
            'id' => 42,
            'foo' => 'baz',
            'created' => '2016-12-23T07:30:30Z',
            'updated' => '2016-12-23T07:30:30Z'
        ], [
            'id' => 42,
            'foo' => 'baz',
            'created' => '2016-12-23T07:30:30Z',
            'updated' => '2016-12-23T08:30:30Z'
        ]);
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->once()
            ->with('/UPDATE studly_caps SET .* WHERE id = 42/')
            ->andReturn($statement);

        $studlyCaps->save($this->em);

        self::assertSame('2016-12-23T08:30:30Z', $studlyCaps->updated);
    }

    public function testUpdatesAfterInsertWithPrimaryKey()
    {
        $studlyCaps = new StudlyCaps(['id' => 42, 'foo' => 'bar']);
        /** @var Mock $statement */
        $statement = \Mockery::mock(\PDOStatement::class);
        $this->pdo->shouldReceive('query')->twice()
                  ->with('/^SELECT .* FROM studly_caps/')
                  ->andReturn($statement);
        $statement->shouldReceive('fetch')->andReturn(false, [
            'id' => 42,
            'foo' => 'bar',
            'created' => '2016-12-23T08:30:30Z'
        ]);
        $this->pdo->shouldReceive('query')->once()
                  ->with('INSERT INTO studly_caps (foo,id) VALUES (\'bar\',42)')
                  ->andReturn(false);

        $studlyCaps->save($this->em);

        self::assertSame('2016-12-23T08:30:30Z', $studlyCaps->created);
    }
}
