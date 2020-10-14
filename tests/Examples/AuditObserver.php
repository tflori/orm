<?php

namespace ORM\Test\Examples;

use ORM\Event;
use ORM\Observer\AbstractObserver;

class AuditObserver extends AbstractObserver
{
    public $log = [];

    public function fetched(Event\Fetched $event)
    {
        $this->writeLog($event);
    }

    public function inserted(Event\Inserted $event)
    {
        $this->writeLog($event, $event->entity->toArray());
    }

    public function updated(Event\Updated $event)
    {
        $this->writeLog($event, $event->dirty);
    }

    public function deleted(Event\Deleted $event)
    {
        $this->writeLog($event);
    }

    protected function writeLog(Event $event, array $data = null)
    {
        $class = get_class($event->entity);

        if (!isset($this->log[$class])) {
            $this->log[$class] = [];
        }

        $this->log[$class][] = [
            'action' => $event::NAME,
            'key' => $event->entity->hasPrimaryKey() ? $event->entity->getPrimaryKey() : null,
            'data' => $data,
        ];
    }
}
