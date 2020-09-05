<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Entity;
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
        $entity = new Article();

        $spy->shouldReceive('__invoke')->with($entity, m::andAnyOthers())->once();

        $observer->on('fetched', $spy);
        $observer->fetched($entity);
    }

    /** @test */
    public function callsRegisteredHandlersInTheirProvidedOrder()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $entity = new Article();

        $spy1->shouldReceive('__invoke')->with($entity, m::andAnyOthers())->once()->ordered();
        $spy2->shouldReceive('__invoke')->with($entity, m::andAnyOthers())->once()->ordered();

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->fetched($entity);
    }

    /** @test */
    public function stopsWhenAHandlerReturnsFalse()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $entity = new Article();

        $spy1->shouldReceive('__invoke')->with($entity, m::andAnyOthers())->once()->andReturnFalse();
        $spy2->shouldNotReceive('__invoke');

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->fetched($entity);
    }

    /** @test */
    public function allHandlersForTheEventAreRemoved()
    {
        $observer = new CallbackObserver();
        $spy1 = m::spy(function (Entity $entity) {
        });
        $spy2 = m::spy(function (Entity $entity) {
        });
        $entity = new Article();

        $spy1->shouldNotReceive('__invoke');
        $spy2->shouldNotReceive('__invoke');

        $observer->on('fetched', $spy1);
        $observer->on('fetched', $spy2);
        $observer->off('fetched');
        $observer->fetched($entity);
    }

    /** @test
     * @param string $event
     * @dataProvider provideEvents*/
    public function acceptsAllKindOfHandlers($event)
    {
        $observer = new CallbackObserver();
        $spy = m::spy(function (Entity $entity) {
        });
        $entity = new Article();

        $spy->shouldReceive('__invoke')->once()->with($entity, m::andAnyOthers());

        $observer->on($event, $spy);
        call_user_func([$observer, $event], $entity);
    }

    public function provideEvents()
    {
        return [
            ['fetched'],
            ['saving'],
            ['saved'],
            ['inserting'],
            ['inserted'],
            ['updating'],
            ['updated'],
            ['deleting'],
            ['deleted'],
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
