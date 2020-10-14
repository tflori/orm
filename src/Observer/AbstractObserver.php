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
    /** @inheritDoc */
    public function handle(Event $event)
    {
        if (is_callable([$this, $event::NAME])) {
            return call_user_func([$this, $event::NAME], $event);
        }

        return true;
    }
}
