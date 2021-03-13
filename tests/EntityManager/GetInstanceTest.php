<?php

namespace ORM\Test\EntityManager;

use Mockery as m;
use ORM\EM;
use ORM\Test\EntityManager\Examples\Concrete;
use ORM\Test\EntityManager\Examples\Entity;
use ORM\Test\EntityManager\Examples\Unspecified;
use ORM\Test\TestEntityManager;
use PHPUnit\Framework\TestCase;

class GetInstanceTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        TestEntityManager::resetStaticsForTest();
    }

    /** @test */
    public function returnsTheLastCreatedEM()
    {
        $em = new EM();
        $last = new EM();

        self::assertSame($last, EM::getInstance());
    }

    /** @test */
    public function returnsLastCreatedFromOutsiteClass()
    {
        require_once __DIR__ . '/Examples/functions.php';
        $last = new EM();

        $em = getLastEmInstance();

        self::assertSame($last, $em);
    }

    /** @test */
    public function defineForNamespace()
    {
        $em = new EM();

        $em->defineForNamespace(Examples\SubNamespace::class);

        self::assertSame($em, EM::getInstance(Examples\SubNamespace\Entity::class));
    }

    /** @test */
    public function defineForParent()
    {
        $em = new EM();

        $em->defineForParent(Entity::class);

        self::assertSame($em, EM::getInstance(Concrete::class));
    }

    /** @test */
    public function returnsLastIfNotSpecified()
    {
        $em = new EM();
        $em->defineForNamespace(Examples\SubNamespace::class);
        $em = new EM();
        $em->defineForParent(Entity::class);
        $last = new EM();

        self::assertSame($last, EM::getInstance(Unspecified::class));
    }

    /** @test */
    public function usesTheResolverProvided()
    {
        $em = new EM();
        $spy = m::spy(function ($class = null) use ($em) {
            return $em;
        });
        EM::setResolver($spy);

        $spy->shouldReceive('__invoke')->with(Concrete::class)->once()->andReturn($em);

        self::assertSame($em, EM::getInstance(Concrete::class));
    }

    /** @test */
    public function registeringNameSpaceHasNoEffectAfterReceivingInstance()
    {
        $em1 = new EM();
        EM::getInstance(Examples\Concrete::class);

        $em2 = new EM();
        $em2->defineForNamespace(Examples::class);

        self::assertSame($em1, EM::getInstance(Examples\Concrete::class));
    }
}
