<?php

namespace ORM;

/**
 * @property-read Entity $entity
 * @property-read array $data
 */
abstract class Event
{
    const NAME = 'event';

    /** @var Entity */
    protected $entity;

    /** @var array */
    protected $data;

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
}
