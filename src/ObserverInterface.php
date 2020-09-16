<?php

namespace ORM;

/**
 * AbstractObserver for entity events
 *
 * When a handler returns false it will cancel other event handlers and if
 * applicable stops the execution (saving, inserting, updating and deleting
 * can be canceled).
 */
interface ObserverInterface
{
    /**
     * Gets called when ever an Entity is fetched from an EntityFetcher or
     * with the parameter $fromDatabase = true.
     *
     * @param Event\Fetched $event
     * @return bool
     */
    public function fetched(Event\Fetched $event);

    /**
     * Gets called when an attribute got changed (using the setAttribute method)
     *
     * @param Event\Changed $event
     * @return bool
     */
    public function changed(Event\Changed $event);

    /**
     * Gets called before an entity gets saved.
     *
     * @param Event\Saving $event
     * @return bool
     */
    public function saving(Event\Saving $event);

    /**
     * Gets called after an entity got saved.
     *
     * @param Event\Saved $event
     * @return bool
     */
    public function saved(Event\Saved $event);

    /**
     * Gets Called before an entity gets inserted.
     *
     * @param Event\Inserting $event
     * @return bool
     */
    public function inserting(Event\Inserting $event);

    /**
     * Gets called after an entity gets inserted.
     *
     * @param Event\Inserted $event
     * @return bool
     */
    public function inserted(Event\Inserted $event);

    /**
     * Gets called before an entity gets updated.
     *
     * @param Event\Updating $event
     * @return bool
     */
    public function updating(Event\Updating $event);

    /**
     * Gets called after an entity got updated.
     *
     * @param Event\Updated $event
     * @return bool
     */
    public function updated(Event\Updated $event);

    /**
     * Gets called before an entity gets deleted.
     *
     * @param Event\Deleting $event
     * @return bool
     */
    public function deleting(Event\Deleting $event);

    /**
     * Gets called after an entity got deleted.
     *
     * @param Event\Deleted $event
     * @return bool
     */
    public function deleted(Event\Deleted $event);
}
