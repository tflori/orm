<?php

namespace ORM\Testing;

use Mockery as m;
use ORM\Entity;
use PDO;
use PDOStatement;

trait MocksEntityFetcher
{
    protected $fetcherResults = [];

    /** @var m\MockInterface|PDO */
    protected $pdo;

    /**
     * Initialize the EntityFetcher Mock
     *
     * Hooks on the PDO that the EntityManager is using. It emulates the results of queries starting with
     * `SELECT DISTINCT t0.* FROM ` and `SELECT COUNT(DISTINCT t0.*) FROM `
     *
     * Please note that every `$em->fetch()` is allowed after initialization.
     *
     * @param m\MockInterface $pdo
     */
    protected function initFetcher(m\MockInterface $pdo)
    {
        $this->pdo = $pdo;
        $pdo->shouldReceive('query')->with(m::pattern('/^SELECT DISTINCT t0\.\* FROM /'))
            ->andReturnUsing(function ($query) {
                return $this->buildStatementMock($query);
            })->byDefault();
        $pdo->shouldReceive('query')->with(m::pattern('/^SELECT COUNT\(DISTINCT t0\.\*\) FROM /'))
            ->andReturnUsing(function ($query) {
                $results = $this->getResultsForQuery($query);

                $statement = m::mock(PDOStatement::class);
                $statement->shouldReceive('fetchColumn')->with()
                    ->andReturn(count($results))
                    ->once();

                return $statement;
            })->byDefault();
    }

    /**
     * Add a result for $class
     *
     * The conditions are regular expressions that have to match the query.
     *
     * Please note that the entities are not the result - new entities will be generated.
     *
     * @param string $class
     * @param array $conditions
     * @param Entity ...$entities
     */
    protected function addFetcherResult($class, array $conditions, Entity ...$entities)
    {
        /** @var Entity|string $class */
        $table = $class::getTableName();

        // complete the primary keys with random numbers
        foreach ($entities as $entity) {
            foreach ($entity::getPrimaryKeyVars() as $attribute) {
                if (!$entity->$attribute) {
                    $entity->$attribute = mt_rand(1000000000, 1000999999);
                }
            }
        }

        if (!isset($this->fetcherResults[$table])) {
            $this->fetcherResults[$table] = [[
                'conditions' => $conditions,
                'entities' => $entities,
            ]];
            return;
        }

        foreach ($this->fetcherResults[$table] as $fetcherResult) {
            if ($fetcherResult['condition'] == $conditions) {
                $fetcherResult['entities'] = $entities;
                return;
            }
        }

        $fetcherResult[$table][] = [
            'conditions' => $conditions,
            'entities' => $entities
        ];
    }

    /**
     * Add a result and expect that it get's fetched
     *
     * The conditions are regular expressions that have to match the query.
     *
     * Please note that the entities are not the result - new entities will be generated.
     *
     * @param string $class
     * @param array $conditions
     * @param Entity ...$entities
     */
    protected function expectFetch($class, array $conditions, Entity ...$entities)
    {
        $this->addFetcherResult($class, $conditions, ...$entities);

        $this->pdo->shouldReceive('query')->withArgs(function ($query) use ($class, $conditions) {
            if (!preg_match('/^SELECT DISTINCT t0\.\* FROM (.*?) AS t0/', $query, $match)) {
                return false;
            }

            /** @var Entity|string $class */
            $table = str_replace('"', '', $match[1]);
            if ($class::getTableName() !== $table) {
                return false;
            }

            return $this->queryMatchesConditions($query, $conditions);
        })->atLeast()->once()->andReturnUsing(function ($query) {
            return $this->buildStatementMock($query);
        });
    }

    /**
     * Internal method to determine the results
     *
     * @param $query
     * @return array
     * @internal
     */
    protected function getResultsForQuery($query)
    {
        if (!preg_match('/FROM (.*?) AS t0/', $query, $match)) {
            return [];
        }

        $table = str_replace('"', '', $match[1]);
        if (!isset($this->fetcherResults[$table])) {
            return [];
        }

        $unconditional = [];
        foreach ($this->fetcherResults[$table] as $fetcherResult) {
            if (empty($fetcherResult['conditions'])) {
                $unconditional = $fetcherResult['entities'];
                continue;
            }

            if ($this->queryMatchesConditions($query, $fetcherResult['conditions'])) {
                return array_map(function (Entity $entity) {
                    return $entity->getData();
                }, $fetcherResult['entities']);
            }
        }

        return array_map(function (Entity $entity) {
            return $entity->getData();
        }, $unconditional);
    }

    /**
     * Internal method to create a mock for the PDOStatement
     *
     * @param $query
     * @return m\MockInterface|PDOStatement
     * @internal
     */
    protected function buildStatementMock($query)
    {
        $results = $this->getResultsForQuery($query);
        array_push($results, false);

        $statement = m::mock(PDOStatement::class);
        $statement->shouldReceive('fetch')->with(PDO::FETCH_ASSOC)
            ->andReturn(...$results)
            ->atLeast()->once();

        return $statement;
    }

    /**
     * Internal method to check if $query matches all $conditions
     *
     * You may want to overwrite this with your own implementation. For now it assumes every condition is a regular
     * expression that should match.
     *
     * @param string $query
     * @param array $conditions
     * @return bool
     */
    protected function queryMatchesConditions($query, array $conditions)
    {
        foreach ($conditions as $condition) {
            if (!preg_match($condition, $query)) {
                return false;
            }
        }

        return true;
    }
}
