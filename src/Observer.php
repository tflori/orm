<?php /** @noinspection PhpUnusedParameterInspection */

namespace ORM;

/**
 * Observer for entity events
 *
 * When a handler returns false it will cancel other event handlers and if
 * applicable stops the execution (saving, inserting, updating and deleting
 * can be canceled).
 */
abstract class Observer
{
    /**
     * Gets called when ever an Entity is fetched from an EntityFetcher or
     * with the parameter $fromDatabase = true.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function fetched(Entity $entity)
    {
        return true;
    }

    /**
     * Gets called before an entity gets saved.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function saving(Entity $entity)
    {
        return true;
    }

    /**
     * Gets called after an entity got saved.
     *
     * Dirty is null after an insert but contains the result from
     * $entity->getDirty() before it got updated.
     *
     * @param Entity $entity
     * @param array $dirty
     * @return bool
     * @codeCoverageIgnore
     */
    public function saved(Entity $entity, array $dirty = null)
    {
        return true;
    }

    /**
     * Gets Called before an entity gets inserted.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function inserting(Entity $entity)
    {
        return true;
    }

    /**
     * Gets called after an entity gets inserted.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function inserted(Entity $entity)
    {
        return true;
    }

    /**
     * Gets called before an entity gets updated.
     *
     * @param Entity $entity
     * @param array|null $dirty
     * @return bool
     * @codeCoverageIgnore
     */
    public function updating(Entity $entity, array $dirty = null)
    {
        return true;
    }

    /**
     * Gets called after an entity got updated.
     *
     * @param Entity $entity
     * @param array|null $dirty
     * @return bool
     * @codeCoverageIgnore
     */
    public function updated(Entity $entity, array $dirty = null)
    {
        return true;
    }

    /**
     * Gets called before an entity gets deleted.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function deleting(Entity $entity)
    {
        return true;
    }

    /**
     * Gets called after an entity got deleted.
     *
     * @param Entity $entity
     * @return bool
     * @codeCoverageIgnore
     */
    public function deleted(Entity $entity)
    {
        return true;
    }
}
