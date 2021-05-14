<?php

namespace ORM\EntityFetcher;

use ORM\EntityFetcher;

interface FilterInterface
{
    /**
     * Apply this filter to $fetcher
     *
     * @param EntityFetcher $fetcher
     * @return void
     */
    public function apply(EntityFetcher $fetcher);
}
