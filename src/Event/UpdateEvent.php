<?php

namespace ORM\Event;

use ORM\Entity;
use ORM\Event;

/**
 * @property-read array $dirty
 */
abstract class UpdateEvent extends Event
{
    /** @var array */
    protected $dirty;

    public function __construct(Entity $entity, array $dirty)
    {
        parent::__construct($entity);

        $this->dirty = $dirty;
    }
}
