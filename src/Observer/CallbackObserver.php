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

    public function on($event, callable $callback)
    {
        if (!isset($this->callbacks[$event])) {
            throw new InvalidArgument(
                'Unknown event ' . $event . '. Use one of ' . implode(',', array_keys($this->callbacks))
            );
        }
        $this->callbacks[$event][] = $callback;
        return $this;
    }

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

    public function saving(Entity $model, array $dirty = null)
    {
        return $this->executeCallbacks('saving', $model, true, $dirty);
    }

    public function saved(Entity $model, array $dirty = null)
    {
        $this->executeCallbacks('saved', $model, false, $dirty);
    }

    public function inserting(Entity $model, array $dirty = null)
    {
        return $this->executeCallbacks('inserting', $model, true, $dirty);
    }

    public function inserted(Entity $model, array $dirty = null)
    {
        $this->executeCallbacks('inserted', $model, false, $dirty);
    }

    public function updating(Entity $model, array $dirty = null)
    {
        return $this->executeCallbacks('updating', $model, true, $dirty);
    }

    public function updated(Entity $model, array $dirty = null)
    {
        $this->executeCallbacks('updated', $model, false, $dirty);
    }

    public function deleting(Entity $model)
    {
        return $this->executeCallbacks('deleting', $model, true);
    }

    public function deleted(Entity $model)
    {
        $this->executeCallbacks('deleted', $model, false);
    }

    protected function executeCallbacks($event, $model, $cancelable = false, array $dirty = null)
    {
        foreach ($this->callbacks[$event] as $callback) {
            $continue = call_user_func($callback, $model, $dirty);
            if ($cancelable && $continue === false) {
                return false;
            }
        }

        return true;
    }
}
