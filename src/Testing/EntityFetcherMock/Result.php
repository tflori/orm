<?php

namespace ORM\Testing\EntityFetcherMock;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\QueryBuilder\QueryBuilder;

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

        // check if joins match
        foreach ($this->joins as $condition) {
            if (!in_array($condition, $fetcher->joins)) {
                return 0;
            }
            $result++;
        }

        // check if where conditions match
        foreach ($this->where as $condition) {
            if (!in_array($condition, $fetcher->where)) {
                return 0;
            }
            $result++;
        }

        // check if grouping matches
        foreach ($this->groupBy as $condition) {
            if (!in_array($condition, $fetcher->groupBy)) {
                return 0;
            }
            $result++;
        }

        // check if order matches
        foreach ($this->orderBy as $condition) {
            if (!in_array($condition, $fetcher->orderBy)) {
                return 0;
            }
            $result++;
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
        array_push($this->entities, ...$entities);
        return $this;
    }

    /**
     * Get the entites for this result
     *
     * @return Entity[]
     * @codeCoverageIgnore trivial code
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
