<?php

namespace ORM\Testing\EntityFetcherMock;

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
     * @param Entity[] $entities
     * @return $this
     * @codeCoverageIgnore trivial code
     */
    public function addEntities(Entity ...$entities)
    {
        array_push($this->entities, ...$entities);
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
}
