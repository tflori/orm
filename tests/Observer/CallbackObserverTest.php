<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Entity;
use ORM\Event;
use ORM\Event\Changed;
use ORM\Event\Deleted;
use ORM\Event\Deleting;
use ORM\Event\Fetched;
use ORM\Event\Inserted;
use ORM\Event\Inserting;
use ORM\Event\Saved;
use ORM\Event\Saving;
use ORM\Event\Updated;
use ORM\Event\Updating;
use ORM\Exception\InvalidArgument;
use ORM\Observer\CallbackObserver;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class CallbackObserverTest extends TestCase
{
    /** @test */
    public function callsRegisteredHandler()
    {
        $observer = new CallbackObserver();
        $spy = m::spy(function (Entity $entity) {
        });
        $event = new Fetched(new Article(), []);

        $spy->shouldReceive('__invoke')->with($event)->once();

        $observer->on('fetched', $spy);
        $observer->fetched($event);
    }

    /** @test */
    public function callsRegisteredHandlersInTheirProvidedOrder()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $event = new Fetched(new Article(), []);

        $spy1->shouldReceive('__invoke')->with($event)->once()->ordered();
        $spy2->shouldReceive('__invoke')->with($event)->once()->ordered();

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->fetched($event);
    }

    /** @test */
    public function stopsWhenAHandlerReturnsFalse()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $event = new Fetched(new Article(), []);

        $spy1->shouldReceive('__invoke')->with($event)->once()->andReturnFalse();
        $spy2->shouldNotReceive('__invoke');

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->fetched($event);
    }

    /** @test */
    public function allHandlersForTheEventAreRemoved()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $event = new Fetched(new Article(), []);

        $spy1->shouldNotReceive('__invoke');
        $spy2->shouldNotReceive('__invoke');

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->off('fetched');
        $observer->fetched($event);
    }

    /** @test
     * @param Event $event
     * @dataProvider provideEvents*/
    public function acceptsAllKindOfHandlers(Event $event)
    {
        $observer = new CallbackObserver();
        $spy = m::spy(function (Entity $entity) {
        });

        $spy->shouldReceive('__invoke')->once()->with($event);

        $observer->on($event::NAME, $spy);
        call_user_func([$observer, $event::NAME], $event);
    }

    public function provideEvents()
    {
        return [
            [new Fetched(new Article(), [])],
            [new Changed(new Article(), 'title', 'Foo', 'Bar')],
            [new Saving(new Article())],
            [new Saved(new Inserted(new Article()))],
            [new Inserting(new Article())],
            [new Inserted(new Article())],
            [new Updating(new Article(), ['title' => ['Foo', 'Bar']])],
            [new Updated(new Article(), ['title' => ['Foo', 'Bar']])],
            [new Deleting(new Article())],
            [new Deleted(new Article())],
        ];
    }

    /** @test */
    public function throwsWhenTryingToRegisterAHandlerForUnknownEvent()
    {
        $observer = new CallbackObserver();

        self::expectException(InvalidArgument::class);

        $observer->on('anything', function () {
        });
    }

    /** @test */
    public function throwsWhenTryingToRemoveHandlersForUnknownEvent()
    {
        $observer = new CallbackObserver();

        self::expectException(InvalidArgument::class);

        $observer->off('anything');
    }
}
