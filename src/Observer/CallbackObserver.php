<?php

namespace ORM\Observer;

use ORM\Event;
use ORM\Exception\InvalidArgument;

class CallbackObserver extends AbstractObserver
{
    protected $handlers = [];

    /**
     * Register a new $listener for $event
     *
     * @param $event
     * @param callable $listener
     * @return static
     */
    public function on($event, callable $listener)
    {
        if (!isset($this->handlers[$event])) {
            $this->handlers[$event] = [];
        }
        $this->handlers[$event][] = $listener;
        return $this;
    }

    /**
     * Remove all listeners for $event
     *
     * @param $event
     * @return static
     */
    public function off($event)
    {
        $this->handlers[$event] = [];
        return $this;
    }

    public function handle(Event $event)
    {
        $handlers = isset($this->handlers[$event::NAME]) ? $this->handlers[$event::NAME] : [];
        foreach ($handlers as $handler) {
            if (call_user_func($handler, $event) === false || $event->stopped) {
                return $event->stopped;
            }
        }

        return true;
    }
}
