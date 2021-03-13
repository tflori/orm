<?php

namespace ORM\EntityFetcher;

use ORM\EntityFetcher;

interface FilterInterface
{
    public function apply(EntityFetcher $fetcher);
}
