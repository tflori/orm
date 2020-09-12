<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Entity;
use ORM\Exception\InvalidArgument;
use ORM\Observer\CallbackObserver;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\User;
use ORM\Test\Examples\AuditObserver;
use ORM\Test\TestCase;

class ObserverRegistrationTest extends TestCase
{
    /** @test */
    public function observersCanBeRegisteredForConcreteEntity()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Article::class, $observer);

        $observer->shouldReceive('fetched')->once();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }

    /** @test */
    public function observersCanBeRegisteredForAParent()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Entity::class, $observer);

        $observer->shouldReceive('fetched')->twice();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }

    /** @test */
    public function observersCanBeRegisteredForMultipleEntities()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Article::class, $observer);
        $this->em->observe(User::class, $observer);

        $observer->shouldReceive('fetched')->twice();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }
    
    /** @test */
    public function attachedObserversCanBeDetached()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Article::class, $observer);
        $this->em->detach($observer);

        $observer->shouldNotReceive('fetched');

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
    }

    /** @test */
    public function detachRemovesItFromAllClasses()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Article::class, $observer);
        $this->em->observe(User::class, $observer);
        $this->em->detach($observer);

        $observer->shouldNotReceive('fetched');

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }

    /** @test */
    public function detachWithClassRemovesItFromThisClass()
    {
        $observer = m::mock(AuditObserver::class);
        $this->em->observe(Article::class, $observer);
        $this->em->observe(User::class, $observer);
        $this->em->detach($observer, User::class);

        $observer->shouldReceive('fetched')->with(m::type(Article::class), m::andAnyOthers())->once();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }

    /** @test */
    public function throwsWhenObserverIsAlreadyAttached()
    {
        $observer = new AuditObserver();
        $this->em->observe(Entity::class, $observer);

        self::expectException(InvalidArgument::class);

        $this->em->observe(Entity::class, $observer);
    }

    /** @test */
    public function allowsASecondObserverOfTheSameType()
    {
        // creating two identical observers
        $observer1 = new CallbackObserver();
        $observer2 = new CallbackObserver();
        $this->em->observe(Entity::class, $observer1);
        $this->em->observe(Entity::class, $observer2);

        $spy = m::spy(function ($entity) {
        });
        $observer1->on('fetched', $spy);
        $observer2->on('fetched', $spy);

        // the spy is registered to both observers
        $spy->shouldReceive('__invoke')->twice();


        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
    }

    /** @test */
    public function removesOnlyOneObserver()
    {
        // creating two identical observers
        $observer1 = new CallbackObserver();
        $observer2 = new CallbackObserver();
        $this->em->observe(Entity::class, $observer1);
        $this->em->observe(Entity::class, $observer2);

        $this->em->detach($observer2, Entity::class);

        $spy1 = m::spy(function ($entity) {});
        $spy2 = m::spy(function ($entity) {});
        $observer1->on('fetched', $spy1);
        $observer2->on('fetched', $spy2);

        $spy1->shouldReceive('__invoke')->once();
        $spy2->shouldNotReceive('__invoke');

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
    }
}
