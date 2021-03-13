<?php

namespace ORM\Test\Entity\Examples\Concerns;

use DateTime;
use DateTimeZone;
use ORM\EM;
use ORM\Event;

trait WithCreated
{
    protected static function bootWithCreated()
    {
        EM::getInstance(static::class)->observe(static::class)
            ->on('inserting', function (Event $event) {
                $now = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.u\Z');
                $event->entity->setAttribute('updated', $now);
            });
    }
}
