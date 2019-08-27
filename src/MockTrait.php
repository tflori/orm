<?php

namespace ORM;

use ORM\Testing\MocksEntityFetcher;
use ORM\Testing\MocksEntityManager;

/**
 * Alias for new namespace
 *
 * @package ORM
 */
trait MockTrait
{
    use MocksEntityManager, MocksEntityFetcher;
}
