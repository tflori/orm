<?php

namespace ORM\Observer;

use ORM\Event;
use ORM\Exception\InvalidArgument;

class CallbackObserver extends AbstractObserver
{
    protected $callbacks = [];

    /**
     * Register a new $listener for $event
     *
     * @param $event
     * @param callable $listener
     * @return $this
     */
    public function on($event, callable $listener)
    {
        if (!isset($this->callbacks[$event])) {
            $this->callbacks[$event] = [];
        }
        $this->callbacks[$event][] = $listener;
        return $this;
    }

    /**
     * Remove all listeners for $event
     *
     * @param $event
     * @return $this
     */
    public function off($event)
    {
        $this->callbacks[$event] = [];
        return $this;
    }

    public function handle(Event $event)
    {
        foreach ($this->callbacks[$event::NAME] as $callback) {
            if (call_user_func($callback, $event) === false) {
                return false;
            }
        }

        return true;
    }
}
