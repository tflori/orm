<?php

namespace ORM\Test\Entity;

use ORM\Test\TestCase;

class BootingTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        BootTestEntity::resetBooting();
    }

    /** @test */
    public function entityGetsBootedWhenEntityGetsCreated()
    {
        BootTestEntity::$bootSpy = $spy = \Mockery::spy(function () {
        });

        $bte = new BootTestEntity();

        $spy->shouldHaveBeenCalled()->once();
    }

    /** @test */
    public function entityGetsBootedOnlyOnce()
    {
        BootTestEntity::$bootSpy = $spy = \Mockery::spy(function () {
        });

        $bte = new BootTestEntity();
        $bte = new BootTestEntity();

        $spy->shouldHaveBeenCalled()->once();
    }

    /** @test */
    public function entityGetsBootedOnUnserialize()
    {
        $bteCache = serialize(new BootTestEntity());
        BootTestEntity::resetBooting();

        BootTestEntity::$bootSpy = $spy = \Mockery::spy(function () {
        });

        $bte = unserialize($bteCache);

        $spy->shouldHaveBeenCalled()->once();
    }

    /** @test */
    public function entityGetsBootedOnCreatingEntityFetcher()
    {
        BootTestEntity::$bootSpy = $spy = \Mockery::spy(function () {
        });

        $fetcher = BootTestEntity::query();

        $spy->shouldHaveBeenCalled()->once();
    }

    /** @test */
    public function traitGetsBooted()
    {
        BootTestEntity::$bootTestTraitSpy = $spy = \Mockery::spy(function () {
        });

        new BootTestEntity();

        $spy->shouldHaveBeenCalled()->once();
    }
}
