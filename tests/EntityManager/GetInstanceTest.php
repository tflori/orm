<?php

namespace ORM\Test\EntityManager;

use ORM\EntityManager;
use ORM\Test\EntityManager\Examples\Concrete;
use ORM\Test\EntityManager\Examples\Entity;
use ORM\Test\EntityManager\Examples\Unspecified;
use PHPUnit\Framework\TestCase;

class GetInstanceTest extends TestCase
{

    public function testReturnsTheLastCreatedEntityManager()
    {
        $em = new EntityManager();
        $last = new EntityManager();

        self::assertSame($last, EntityManager::getInstance());
    }

    public function testReturnsLastCreatedFromOutsiteClass()
    {
        require_once __DIR__ . '/Examples/functions.php';
        $last = new EntityManager();

        $em = getLastEmInstance();

        self::assertSame($last, $em);
    }

    public function testDefineForNamespace()
    {
        $em = new EntityManager();

        $em->defineForNamespace(Examples\SubNamespace::class);

        self::assertSame($em, EntityManager::getInstance(Examples\SubNamespace\Entity::class));
    }

    public function testDefineForParent()
    {
        $em = new EntityManager();

        $em->defineForParent(Entity::class);

        self::assertSame($em, EntityManager::getInstance(Concrete::class));
    }

    public function testReturnsLastIfNotSpecified()
    {
        $em = new EntityManager();
        $em->defineForNamespace(Examples\SubNamespace::class);
        $em = new EntityManager();
        $em->defineForParent(Entity::class);
        $last = new EntityManager();

        self::assertSame($last, EntityManager::getInstance(Unspecified::class));
    }
}
