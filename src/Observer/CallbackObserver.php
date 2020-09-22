<?php

namespace ORM\Observer;

use ORM\Event;
use ORM\Exception\InvalidArgument;

class CallbackObserver extends AbstractObserver
{
    protected $callbacks = [
        'fetched' => [],
        'changed' => [],
        'saving' => [],
        'saved' => [],
        'inserting' => [],
        'inserted' => [],
        'updating' => [],
        'updated' => [],
        'deleting' => [],
        'deleted' => [],
    ];

    /**
     * Register a new $listener for $event
     *
     * @param $event
     * @param callable $listener
     * @return $this
     * @throws InvalidArgument
     */
    public function on($event, callable $listener)
    {
        if (!isset($this->callbacks[$event])) {
            throw new InvalidArgument(
                'Unknown event ' . $event . '. Use one of ' . implode(',', array_keys($this->callbacks))
            );
        }
        $this->callbacks[$event][] = $listener;
        return $this;
    }

    /**
     * Remove all listeners for $event
     *
     * @param $event
     * @return $this
     * @throws InvalidArgument
     */
    public function off($event)
    {
        if (!isset($this->callbacks[$event])) {
            throw new InvalidArgument(
                'Unknown event ' . $event . '. Use one of ' . implode(',', array_keys($this->callbacks))
            );
        }
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
