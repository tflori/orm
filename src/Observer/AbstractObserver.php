<?php /** @noinspection PhpUnusedParameterInspection */

namespace ORM\Observer;

use ORM\Event;
use ORM\ObserverInterface;

/**
 * AbstractObserver for entity events
 *
 * When a handler returns false it will cancel other event handlers and if
 * applicable stops the execution (saving, inserting, updating and deleting
 * can be canceled).
 */
abstract class AbstractObserver implements ObserverInterface
{
    /** @inheritDoc
     * @codeCoverageIgnore */
    public function fetched(Event\Fetched $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function saving(Event\Saving $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function saved(Event\Saved $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function inserting(Event\Inserting $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function inserted(Event\Inserted $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function updating(Event\Updating $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function updated(Event\Updated $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function deleting(Event\Deleting $event)
    {
        return true;
    }

    /** @inheritDoc
     * @codeCoverageIgnore */
    public function deleted(Event\Deleted $event)
    {
        return true;
    }
}
