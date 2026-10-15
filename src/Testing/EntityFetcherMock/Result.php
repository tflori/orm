<?php

namespace ORM\Testing\EntityFetcherMock;

use Mockery as m;
use Mockery\ExpectationDirector;
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Exception;

class Result extends EntityFetcher
{
    /** @var Entity[] */
    protected $entities = [];

    /** @var string[] */
    protected $regularExpressions = [];

    /** Methods with expectations (delete, update and insert are only handled by this result when expected)
     * @var bool[] */
    protected $expectedMethods = [];

    /**
     * Check if $fetcher matches the current query
     *
     * Returns the score for the given EntityFetcher. The more conditions match the higher the score:
     * - 0 = the query does not match one of the conditions
     * - 1 = no conditions required to match the query
     * - n = n-1 conditions matched the query
     *
     * @param EntityFetcher $fetcher
     * @return int
     */
    public function compare(EntityFetcher $fetcher)
    {
        $result = 1;

        // joins, grouping and ordering are just lists so they have to exist
        foreach (['joins', 'groupBy', 'orderBy'] as $attribute) {
            foreach ($this->$attribute as $condition) {
                if (!in_array($condition, $fetcher->$attribute)) {
                    return 0;
                }
                $result++;
            }
        }

        // where conditions can have 'AND ' or 'OR ' in front
        // there is a lot of logic behind these keywords that we ignore here
        foreach ($this->where as $condition) {
            $condition = preg_replace('/^(AND |OR )/', '', $condition);
            foreach ($fetcher->where as $fetcherCondition) {
                $fetcherCondition = preg_replace('/^(AND |OR )/', '', $fetcherCondition);
                if ($condition === $fetcherCondition) {
                    $result++;
                    continue 2; // continue the outer foreach to not execute return 0
                }
            }
            return 0; // this is only reached when no condition matched
        }

        // check if limit and offset matches
        if ($this->limit) {
            if ($this->limit !== $fetcher->limit || $this->offset !== $fetcher->offset) {
                return 0;
            }
            $result++;
        }

        // check if regular expressions match
        foreach ($this->regularExpressions as $expression) {
            if (!preg_match($expression, $fetcher->getQuery())) {
                return 0;
            }
            $result++;
        }

        return $result;
    }

    /**
     * Add a regular expression that has to match
     *
     * @param string $expression
     * @return $this
     * @codeCoverageIgnore trivial code
     */
    public function matches($expression)
    {
        $this->regularExpressions[] = $expression;
        return $this;
    }

    /**
     * Add entities to the result
     *
     * @param Entity ...$entities
     * @return $this
     * @codeCoverageIgnore trivial code
     */
    public function addEntities(Entity ...$entities)
    {
        if (!empty($entities)) {
            array_push($this->entities, ...$entities);
        }
        return $this;
    }

    /**
     * Get the entities for this result
     *
     * @return Entity[]
     * @codeCoverageIgnore trivial code
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Get the next entity for $fetcher
     *
     * Every fetcher has its own cursor - without $fetcher the cursor of this result is used.
     *
     * @param EntityFetcher|null $fetcher
     * @return ?Entity
     */
    public function one(?EntityFetcher $fetcher = null)
    {
        return $this->next($fetcher ?: $this);
    }

    /**
     * Get the remaining entities for $fetcher
     *
     * Every fetcher has its own cursor - without $fetcher the cursor of this result is used.
     *
     * @param int $limit
     * @param EntityFetcher|null $fetcher
     * @return Entity[]
     */
    public function all($limit = 0, ?EntityFetcher $fetcher = null)
    {
        $fetcher = $fetcher ?: $this;
        $entities = [];
        while ($entity = $this->next($fetcher)) {
            $entities[] = $entity;
            if ($limit && count($entities) >= $limit) {
                break;
            }
        }
        return $entities;
    }

    /**
     * Get the entity at the cursor of $fetcher and move the cursor
     *
     * @param EntityFetcher $fetcher
     * @return ?Entity
     */
    private function next(EntityFetcher $fetcher)
    {
        if (!isset($this->entities[$fetcher->cursor])) {
            return null;
        }
        return $this->entities[$fetcher->cursor++];
    }

    /**
     * Get the count of entities in this result
     *
     * @return int
     */
    public function count()
    {
        return count($this->entities);
    }

    /**
     * Execute a delete statement — returns the count of matched entities
     *
     * @return int
     */
    public function delete()
    {
        return count($this->entities);
    }

    /**
     * Execute an update statement — returns the count of matched entities
     *
     * @param array $updates
     * @return int
     */
    public function update(array $updates)
    {
        return count($this->entities);
    }

    /**
     * Execute an insert statement — returns the number of rows
     *
     * @param array ...$rows
     * @return int
     */
    public function insert(array ...$rows)
    {
        return count($rows);
    }

    /**
     * Check if an expectation for $method was defined
     *
     * @param string $method
     * @return bool
     * @internal
     */
    public function isExpected($method)
    {
        return isset($this->expectedMethods[$method]);
    }

    /**
     * Expect one() to be called on the matching fetcher
     *
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @return ResultExpectation
     */
    public function expectOne()
    {
        return $this->expect('one');
    }

    /**
     * Expect all() to be called on the matching fetcher
     *
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @return ResultExpectation
     */
    public function expectAll()
    {
        return $this->expect('all');
    }

    /**
     * Expect count() to be called on the matching fetcher
     *
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @return ResultExpectation
     */
    public function expectCount()
    {
        return $this->expect('count');
    }

    /**
     * Expect delete() to be called on the matching fetcher
     *
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @return ResultExpectation
     */
    public function expectDelete()
    {
        return $this->expect('delete');
    }

    /**
     * Expect update() to be called on the matching fetcher
     *
     * Optionally validate the update data by passing the expected array (or a Mockery matcher).
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @param mixed $updates
     * @return ResultExpectation
     */
    public function expectUpdate($updates = null)
    {
        $expectation = $this->expect('update');
        if ($updates !== null) {
            $expectation->with($updates);
        }
        return $expectation;
    }

    /**
     * Expect insert() to be called on the matching fetcher
     *
     * Optionally validate the insert data by passing the expected rows (or Mockery matchers).
     * Expects exactly one call by default - chain ->times(n), ->never(), ->atLeast()->once() etc. to change it.
     * Calls the real method unless a return value is defined with ->andReturn*() or ->andThrow().
     *
     * @param mixed ...$rows
     * @return ResultExpectation
     */
    public function expectInsert(...$rows)
    {
        $expectation = $this->expect('insert');
        if (!empty($rows)) {
            $expectation->with(...$rows);
        }
        return $expectation;
    }

    /**
     * Create the expectation for $method
     *
     * @param string $method
     * @return ResultExpectation
     * @throws Exception
     */
    private function expect($method)
    {
        if (!$this instanceof m\MockInterface) {
            throw new Exception(
                'Expectations require a mocked result - use EntityManagerMock::addResult() or ormAddResult()'
            );
        }

        $this->expectedMethods[$method] = true;

        // same as shouldReceive() but with a ResultExpectation
        $director = $this->mockery_getExpectationsFor($method);
        if (!$director) {
            $director = new ExpectationDirector($method, $this);
            $this->mockery_setExpectationsFor($method, $director);
        }
        $expectation = new ResultExpectation($this, $method);
        $director->addExpectation($expectation);

        $expectation->passthru()->once();

        return $expectation;
    }
}
