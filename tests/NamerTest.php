<?php

namespace ORM\Test;

use ORM\EntityManager;
use ORM\Namer;
use ORM\Test\Entity\Examples\Article;

class NamerTest extends TestCase
{
    public function testDefaultTableNameTemplate()
    {
        $namer = new Namer();
        $reflection = new \ReflectionClass(Article::class);

        $result = $namer->getTableName($reflection);

        self::assertSame($namer->getTableName($reflection, '%short%'), $result);
    }

    public function testDefaultTableNamingScheme()
    {
        $namer = new Namer();
        $reflection = new \ReflectionClass(Article::class);

        $result = $namer->getTableName($reflection, '%name%');

        self::assertSame($namer->getTableName($reflection, '%name%', 'snake_lower'), $result);
    }

    public function testTableNameTemplateOption()
    {
        $namer = new Namer([
            EntityManager::OPT_TABLE_NAME_TEMPLATE => '%name%'
        ]);
        $reflection = new \ReflectionClass(Article::class);

        $result = $namer->getTableName($reflection);

        self::assertSame($namer->getTableName($reflection, '%name%'), $result);
    }

    public function testTableNamingSchemeOption()
    {
        $namer = new Namer([
            EntityManager::OPT_NAMING_SCHEME_TABLE => 'StudlyCaps'
        ]);
        $reflection = new \ReflectionClass(Article::class);

        $result = $namer->getTableName($reflection, '%name%');

        self::assertSame($namer->getTableName($reflection, '%name%', 'StudlyCaps'), $result);
    }

    public function testSubstituteEscaping()
    {
        $namer = new Namer();

        $result = $namer->substitute('%%%a%', ['a' => 'short%']);

        self::assertSame('%short%', $result);
    }
}
