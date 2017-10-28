<?php

namespace ORM\Test\EntityManager;

use ORM\EntityManager;
use ORM\Test\EntityManager\Examples\Concrete;
use ORM\Test\EntityManager\Examples\Entity;
use ORM\Test\EntityManager\Examples\Unspecified;
use PHPUnit\Framework\TestCase;

class GetInstanceTest extends TestCase
{

    /** @test */
    public function returnsTheLastCreatedEntityManager()
    {
        $em = new EntityManager();
        $last = new EntityManager();

        self::assertSame($last, EntityManager::getInstance());
    }

    /** @test */
    public function returnsLastCreatedFromOutsiteClass()
    {
        require_once __DIR__ . '/Examples/functions.php';
        $last = new EntityManager();

        $em = getLastEmInstance();

        self::assertSame($last, $em);
    }

    /** @test */
    public function defineForNamespace()
    {
        $em = new EntityManager();

        $em->defineForNamespace(Examples\SubNamespace::class);

        self::assertSame($em, EntityManager::getInstance(Examples\SubNamespace\Entity::class));
    }

    /** @test */
    public function defineForParent()
    {
        $em = new EntityManager();

        $em->defineForParent(Entity::class);

        self::assertSame($em, EntityManager::getInstance(Concrete::class));
    }

    /** @test */
    public function returnsLastIfNotSpecified()
    {
        $em = new EntityManager();
        $em->defineForNamespace(Examples\SubNamespace::class);
        $em = new EntityManager();
        $em->defineForParent(Entity::class);
        $last = new EntityManager();

        self::assertSame($last, EntityManager::getInstance(Unspecified::class));
    }
}
