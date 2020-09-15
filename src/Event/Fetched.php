<?php

namespace ORM\Event;

use ORM\Entity;
use ORM\Event;

class Fetched extends Event
{
    const NAME = 'fetched';

    /** @var array */
    protected $rawData;

    public function __construct(Entity $entity, array $rawData)
    {
        parent::__construct($entity);
        $this->rawData = $rawData;
    }
}
