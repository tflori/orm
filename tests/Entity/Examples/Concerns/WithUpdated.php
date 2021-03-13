<?php

namespace ORM\Test\Entity\Examples\Concerns;

use DateTime;
use DateTimeZone;
use ORM\EM;
use ORM\Event;

trait WithUpdated
{
    protected static function bootWithUpdated()
    {
        EM::getInstance(static::class)->observe(static::class)
            ->on('inserting', function (Event $event) {
                $now = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.u\Z');
                $event->entity->setAttribute('updated', $now);
            })
            ->on('updating', function (Event $event) {
                $now = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.u\Z');
                $event->entity->setAttribute('updated', $now);
            });
    }
}
