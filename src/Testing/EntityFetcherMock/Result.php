<?php

namespace ORM\Testing\EntityFetcherMock;

use ORM\Entity;
use ORM\EntityFetcher;

class Result extends EntityFetcher
{
    /** @var Entity[] */
    protected $entities = [];

    public function addEntities(Entity ...$entities)
    {
        array_push($this->entities, ...$entities);
        return $this;
    }

    public function matchesQuery($query)
    {
        $result = 1;

        // check if joins match

        // check if where conditions match
        foreach ($this->where as $condition) {
            if (strpos($query, $condition) === false) {
                return 0;
            }
            $result++;
        }

        // check if grouping matches
        // check if order matches
        // check if limit and offset matches

        return $result;
    }

    public function getEntities()
    {
        return $this->entities;
    }
}
