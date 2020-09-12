<?php

namespace ORM\Test\Examples;

use ORM\Entity;
use ORM\Observer;

class AuditObserver extends Observer
{
    public $log = [];

    public function fetched(Entity $entity)
    {
        $this->writeLog('fetched', $entity);
    }

    public function inserted(Entity $entity)
    {
        $this->writeLog('inserted', $entity, $entity->toArray());
    }

    public function updated(Entity $entity, array $dirty = null)
    {
        $this->writeLog('updated', $entity, $dirty);
    }

    public function deleted(Entity $entity)
    {
        $this->writeLog('deleted', $entity);
    }

    protected function writeLog($action, Entity $entity, array $data = null)
    {
        $class = get_class($entity);

        if (!isset($this->log[$class])) {
            $this->log[$class] = [];
        }

        $this->log[$class][] = [
            'action' => $action,
            'key' => $entity->hasPrimaryKey() ? $entity->getPrimaryKey() : null,
            'data' => $data,
        ];
    }
}
