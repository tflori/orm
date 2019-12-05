<?php

namespace ORM\Test;

use ORM\EntityManager;
use ORM\Namer;
use ORM\Test\Entity\Examples\Article;

class NamerTest extends TestCase
{
    /** @test */
    public function defaultTableNameTemplate()
    {
        $namer = new Namer();

        $result = $namer->getTableName(Article::class);

        self::assertSame($namer->getTableName(Article::class, '%short%'), $result);
    }

    /** @test */
    public function defaultTableNamingScheme()
    {
        $namer = new Namer();

        $result = $namer->getTableName(Article::class, '%name%');

        self::assertSame($namer->getTableName(Article::class, '%name%', 'snake_lower'), $result);
    }

    /** @test */
    public function defaultColumnNamingScheme()
    {
        $namer = new Namer();

        $result = $namer->getColumnName(Article::class, 'someVar');

        self::assertSame($namer->getColumnName(Article::class, 'someVar', 'snake_lower'), $result);
    }

    /** @test */
    public function defaultMethodNamingScheme()
    {
        $namer = new Namer();

        $result = $namer->getMethodName('get_some_var');

        self::assertSame($namer->getMethodName('get_some_var', 'camelCase'), $result);
    }

    /** @test */
    public function defaultAttributeNamingScheme()
    {
        $namer = new Namer();

        $result = $namer->getAttributeName('user_id');

        self::assertSame('userId', $result); // camelCase
    }

    /** @test */
    public function tableNameTemplateOption()
    {
        $namer = new Namer([
            EntityManager::OPT_TABLE_NAME_TEMPLATE => '%name%'
        ]);

        $result = $namer->getTableName(Article::class);

        self::assertSame($namer->getTableName(Article::class, '%name%'), $result);
    }

    /** @test */
    public function tableNamingSchemeOption()
    {
        $namer = new Namer([
            EntityManager::OPT_NAMING_SCHEME_TABLE => 'StudlyCaps'
        ]);

        $result = $namer->getTableName(Article::class, '%name%');

        self::assertSame($namer->getTableName(Article::class, '%name%', 'StudlyCaps'), $result);
    }

    /** @test */
    public function columnNamingSchemeOption()
    {
        $namer = new Namer([
            EntityManager::OPT_NAMING_SCHEME_COLUMN => 'StudlyCaps'
        ]);

        $result = $namer->getColumnName(Article::class, 'some_var');

        self::assertSame($namer->getColumnName(Article::class, 'some_var', null, 'StudlyCaps'), $result);
    }

    /** @test */
    public function methodNamingSchemeOption()
    {
        $namer = new Namer([
            EntityManager::OPT_NAMING_SCHEME_METHODS => 'snake_lower'
        ]);

        $result = $namer->getMethodName('getSomeVar');

        self::assertSame($namer->getMethodName('getSomeVar', 'snake_lower'), $result);
    }

    /** @test */
    public function attributeNamingSchemeOption()
    {
        $namer = new Namer([
            EntityManager::OPT_NAMING_SCHEME_ATTRIBUTE => 'StudlyCaps'
        ]);

        $result = $namer->getAttributeName('valid_until');

        self::assertSame('ValidUntil', $result);
    }

    /** @test */
    public function substituteEscaping()
    {
        $namer = new Namer();

        $result = $namer->substitute('%%%a%', ['a' => 'short%']);

        self::assertSame('%short%', $result);
    }

    /** @test */
    public function removesPrefixFromColumnNames()
    {
        $namer = new Namer();

        $result = $namer->getAttributeName('usr_remember_token', 'usr_');

        self::assertSame('rememberToken', $result);
    }
}
