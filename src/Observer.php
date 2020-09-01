<?php /** @noinspection PhpUnusedParameterInspection */

namespace ORM;

abstract class Observer
{
    public function fetched(Entity $entity)
    {
    }

    public function saving(Entity $entity, array $dirty = null)
    {
    }

    public function saved(Entity $entity, array $dirty = null)
    {
    }

    public function inserting(Entity $entity, array $dirty = null)
    {
    }

    public function inserted(Entity $entity, array $dirty = null)
    {
    }
    
    public function updating(Entity $entity, array $dirty = null)
    {
    }

    public function updated(Entity $entity, array $dirty = null)
    {
    }

    public function deleting(Entity $entity)
    {
    }

    public function deleted(Entity $entity)
    {
    }
}
