<?php

namespace ORM\Test\EntityFetcher\Examples;

use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;

class NotDeletedFilter implements FilterInterface
{
    public function apply(EntityFetcher $fetcher)
    {
        $fetcher->where('deleted IS NULL');
    }
}
