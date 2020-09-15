<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Entity;
use ORM\Event\Fetched;
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

        $observer->shouldReceive('fetched')->withArgs(function (Fetched $event) {
            return $event->entity instanceof Article;
        })->once();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
        new User(['id' => 23, 'name' => 'John Doe'], $this->em, true);
    }

    /** @test */
    public function returnsFalseIfNotDetached()
    {
        $observer = new AuditObserver();

        $detached = $this->em->detach($observer);

        self::assertFalse($detached);
    }

    /** @test */
    public function returnsTrueIfDetached()
    {
        $observer = new AuditObserver();
        $this->em->observe(Article::class, $observer);

        $detached = $this->em->detach($observer);

        self::assertTrue($detached);
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
        // creating two equal observers
        $observer1 = m::mock(AuditObserver::class);
        $observer2 = m::mock(AuditObserver::class);
        $this->em->observe(Entity::class, $observer1);
        $this->em->observe(Entity::class, $observer2);

        $observer1->shouldReceive('fetched')->once();
        $observer2->shouldReceive('fetched')->once();

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
    }

    /** @test */
    public function removesOnlyOneObserver()
    {
        // creating two equal observers
        $observer1 = m::mock(AuditObserver::class);
        $observer2 = m::mock(AuditObserver::class);
        $this->em->observe(Entity::class, $observer1);
        $this->em->observe(Entity::class, $observer2);

        $this->em->detach($observer2, Entity::class);

        $observer1->shouldReceive('fetched')->once();
        $observer2->shouldNotReceive('fetched');

        new Article(['id' => 123, 'title' => 'Foo Bar'], $this->em, true);
    }
}
