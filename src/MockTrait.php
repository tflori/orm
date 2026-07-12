<?php

namespace ORM;

use ORM\Testing\MocksEntityManager;

/**
 * Alias for new namespace
 *
 * @package ORM
 * @see     MocksEntityManager
 */
trait MockTrait // @phpstan-ignore trait.unused
{
    use MocksEntityManager;
}
