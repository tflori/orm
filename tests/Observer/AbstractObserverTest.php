<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Event\Fetched;
use ORM\Event\Saving;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Examples\AuditObserver;
use ORM\Test\TestCase;

class AbstractObserverTest extends TestCase
{
    /** @test */
    public function callsEventMethodIfAvailable()
    {
        $observer = m::mock(AuditObserver::class)->makePartial();
        $event = new Fetched(new Article(), ['id' => 1, 'title' => 'Foo Bar']);

        $observer->shouldReceive('fetched')->once()->andReturnFalse();

        $result = $observer->handle($event);

        self::assertFalse($result);
    }

    /** @test */
    public function returnsTrueWhenNoMethodIsDefined()
    {
        $observer = new AuditObserver();
        $event = new Saving(new Article());

        $result = $observer->handle($event);

        self::assertTrue($result);
    }
}
