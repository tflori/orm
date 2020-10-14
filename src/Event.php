<?php

namespace ORM;

/**
 * @property-read Entity $entity
 * @property-read array $data
 * @property-read bool $stopped
 */
abstract class Event
{
    const NAME = 'event';

    /** @var Entity */
    protected $entity;

    /** @var array */
    protected $data;

    /** @var bool */
    protected $stopped = false;

    /**
     * @param Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
        $this->data = $entity->getData();
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    public function stop()
    {
        $this->stopped = true;
    }
}
