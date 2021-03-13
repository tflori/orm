<?php

namespace ORM\EntityFetcher;

use ORM\EntityFetcher;

class CallableFilter implements FilterInterface
{
    protected $filter;

    /**
     * @param callable $filter
     */
    public function __construct(callable $filter)
    {
        $this->filter = $filter;
    }

    public function apply(EntityFetcher $fetcher)
    {
        call_user_func($this->filter, $fetcher);
    }
}
