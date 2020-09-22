<?php

namespace ORM\Test\Observer;

use Mockery as m;
use ORM\Event;
use ORM\Observer\AbstractObserver;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\TestCase;

class FireEventTest extends TestCase
{
    /** @test */
    public function returnsTrueWithoutObservers()
    {
        $event = new Event\Fetched(new Article(), ['title' => 'Foo']);

        $result = $this->em->fire($event);

        self::assertTrue($result);
    }

    /** @test */
    public function returnsTrueWhenTheObserverReturnsNull()
    {
        $event = new Event\Fetched(new Article(), ['title' => 'Foo']);
        $observer = m::mock(AbstractObserver::class);
        $this->em->observe(Article::class, $observer);

        $observer->shouldReceive('handle')->once()->andReturnNull();

        $result = $this->em->fire($event);

        self::assertTrue($result);
    }

    /** @test */
    public function returnsFalseWhenTheObserverReturnsFalse()
    {
        $event = new Event\Fetched(new Article(), ['title' => 'Foo']);
        $observer = m::mock(AbstractObserver::class);
        $this->em->observe(Article::class, $observer);

        $observer->shouldReceive('handle')->once()->andReturnFalse();

        $result = $this->em->fire($event);

        self::assertFalse($result);
    }

    /** @test */
    public function stopsWhenAnObserverReturnsFalse()
    {
        $event = new Event\Fetched(new Article(), ['title' => 'Foo']);
        $observer = m::mock(AbstractObserver::class);
        $this->em->observe(Article::class, $observer);
        $observer2 = m::mock(AbstractObserver::class);
        $this->em->observe(Article::class, $observer2);

        $observer->shouldReceive('handle')->once()->andReturnFalse();
        $observer2->shouldNotReceive('handle');

        $this->em->fire($event);
    }
}
