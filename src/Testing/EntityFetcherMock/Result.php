<?php

namespace ORM\Testing\EntityFetcherMock;

use Mockery as m;
use ORM\Entity;
use ORM\EntityFetcher;

class Result extends EntityFetcher
{
    /** @var Entity[] */
    protected $entities = [];

    /** @var string[] */
    protected $regularExpressions = [];

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
     * Get the next entity from the result
     *
     * @return ?Entity
     */
    public function one()
    {
        if (!isset($this->entities[$this->cursor])) {
            return null;
        }
        return $this->entities[$this->cursor++];
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
     * Expect one() to be called on the matching fetcher
     *
     * Returns a Mockery expectation for further modifiers like ->once() or ->never().
     *
     * @return m\Expectation
     */
    public function expectOne()
    {
        return $this->shouldReceive('one')->passthru();
    }

    /**
     * Expect all() to be called on the matching fetcher
     *
     * Returns a Mockery expectation for further modifiers like ->once() or ->never().
     *
     * @return m\Expectation
     */
    public function expectAll()
    {
        return $this->shouldReceive('all')->passthru();
    }

    /**
     * Expect count() to be called on the matching fetcher
     *
     * Returns a Mockery expectation for further modifiers like ->once() or ->never().
     *
     * @return m\Expectation
     */
    public function expectCount()
    {
        return $this->shouldReceive('count')->passthru();
    }

    /**
     * Expect delete() to be called on the matching fetcher
     *
     * Chain ->once(), ->never(), ->times(n) etc. on the returned expectation.
     *
     * @return m\Expectation
     */
    public function expectDelete()
    {
        return $this->shouldReceive('delete')->passthru();
    }

    /**
     * Expect update() to be called on the matching fetcher
     *
     * Optionally validate the update data by passing the expected array.
     * Chain ->once(), ->never(), ->times(n) etc. on the returned expectation.
     *
     * @param array|null $updates
     * @return m\Expectation
     */
    public function expectUpdate(?array $updates = null)
    {
        $expectation = $this->shouldReceive('update')->passthru();
        if ($updates !== null) {
            $expectation->with($updates);
        }
        return $expectation;
    }

    /**
     * Expect insert() to be called on the matching fetcher
     *
     * Optionally validate the insert data by passing the expected rows.
     * Chain ->once(), ->never(), ->times(n) etc. on the returned expectation.
     *
     * @param array ...$rows
     * @return m\Expectation
     */
    public function expectInsert(array ...$rows)
    {
        $expectation = $this->shouldReceive('insert')->passthru();
        if (!empty($rows)) {
            $expectation->with(...$rows);
        }
        return $expectation;
    }
}
