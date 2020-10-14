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
     * Handles the $event.
     *
     * Return false to stop event execution.
     *
     * @param Event $event
     * @return bool
     */
    public function handle(Event $event);
}
