<?php

namespace ORM\Event;

use ORM\Entity;
use ORM\Event;

/**
 * @property-read array|null $dirty
 */
class Saved extends Event
{
    const NAME = 'saved';

    /** @var Event */
    protected $originalEvent;

    public function __construct(Event $originalEvent)
    {
        parent::__construct($originalEvent->entity);

        $this->originalEvent = $originalEvent;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : $this->originalEvent->$name;
    }
}
