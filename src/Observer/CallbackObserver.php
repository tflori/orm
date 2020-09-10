<?php

namespace ORM\Observer;

use ORM\Entity;
use ORM\Exception\InvalidArgument;
use ORM\Observer;

class CallbackObserver extends Observer
{
    protected $callbacks = [
        'fetched' => [],
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

    public function fetched(Entity $model)
    {
        $this->executeCallbacks('fetched', $model);
    }

    public function saving(Entity $model)
    {
        return $this->executeCallbacks('saving', $model);
    }

    public function saved(Entity $model, array $dirty = null)
    {
        $this->executeCallbacks('saved', $model, $dirty);
    }

    public function inserting(Entity $model)
    {
        return $this->executeCallbacks('inserting', $model);
    }

    public function inserted(Entity $model)
    {
        $this->executeCallbacks('inserted', $model);
    }

    public function updating(Entity $model, array $dirty = null)
    {
        return $this->executeCallbacks('updating', $model, $dirty);
    }

    public function updated(Entity $model, array $dirty = null)
    {
        $this->executeCallbacks('updated', $model, $dirty);
    }

    public function deleting(Entity $model)
    {
        return $this->executeCallbacks('deleting', $model);
    }

    public function deleted(Entity $model)
    {
        $this->executeCallbacks('deleted', $model);
    }

    protected function executeCallbacks($event, $model, array $dirty = null)
    {
        foreach ($this->callbacks[$event] as $callback) {
            if (call_user_func($callback, $model, $dirty) === false) {
                return false;
            }
        }

        return true;
    }
}
