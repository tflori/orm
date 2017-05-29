<?php

namespace ORM\Test\Entity;

use ORM\Dbal\Column;
use ORM\Dbal\Mysql;
use ORM\Dbal\Table;
use ORM\Dbal\Type\Integer;
use ORM\Dbal\Type\Number;
use ORM\Dbal\Type\VarChar;
use ORM\EntityManager;
use ORM\Exception;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Category;
use ORM\Test\TestCase;

class ValidateTest extends TestCase
{
    /** @var Column */
    protected static $columnId;
    /** @var Column */
    protected static $columnTitle;
    /** @var Column */
    protected static $columnIntroText;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $dbal = new Mysql(new EntityManager());

        self::$columnId = \Mockery::mock(Column::class, [$dbal, [
            'column_name' => 'id',
            'column_default' => 'sequence(AUTO_INCREMENT)',
            'is_nullable' => false
        ]])->makePartial();
        self::$columnTitle = \Mockery::mock(Column::class, [$dbal, [
            'column_name' => 'title',
            'column_default' => null,
            'is_nullable' => false
        ]])->makePartial();
        self::$columnIntroText = \Mockery::mock(Column::class, [$dbal, [
            'column_name' => 'intro_text',
            'column_default' => null,
            'is_nullable' => true
        ]])->makePartial();
    }

    public function testInitValidatorWithoutStaticDescription()
    {
        $articleDescription = [self::$columnId, self::$columnTitle, self::$columnIntroText];
        $this->em->shouldReceive('describe')->with('article')->once()->andReturn(new Table($articleDescription));

        Article::initValidator($this->em);
    }

    /**
     * @depends testInitValidatorWithoutStaticDescription
     */
    public function testCallsDescribeOnlyOnce()
    {
        $this->em->shouldNotReceive('describe')->with('article');

        Article::initValidator($this->em);
    }

    /**
     * @depends testInitValidatorWithoutStaticDescription
     */
    public function testIsInitialized()
    {
        $result = Article::validatorIsInitialized();

        self::assertTrue($result);
    }

    public function testIsNotInitialized()
    {
        $result = Category::validatorIsInitialized();

        self::assertFalse($result);
    }

    /**
     * @depends testInitValidatorWithoutStaticDescription
     */
    public function testValidateUsesValidator()
    {
        self::$columnTitle->shouldReceive('validate')->with('Hello World!')->once()->andReturn(true);

        Article::validate('title', 'Hello World!');
    }

    public function testValidateThrowsValidatorNotInitialized()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Validator not initialized yet');

        Category::validate('name', 'News');
    }

    /**
     * @depends testInitValidatorWithoutStaticDescription
     */
    public function testConvertsFieldNamesToColumns()
    {
        self::$columnIntroText->shouldReceive('validate')
            ->with('This is just a test article.')->once()->andReturn(true);

        Article::validate('introText', 'This is just a test article.');
    }

    public function testConvertsFieldNamesToColumnsAgain()
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown column some_thing');

        Article::validate('someThing', 23);
    }

    /**
     * @depends testInitValidatorWithoutStaticDescription
     */
    public function testValidateArray()
    {
        self::$columnTitle->shouldReceive('validate')->with('Hello World!')->once()->andReturn(true);
        self::$columnIntroText->shouldReceive('validate')
            ->with('This is just a test article.')->once()->andReturn(true);

        $result = Article::validateArray([
            'title' => 'Hello World!',
            'introText' => 'This is just a test article.',
        ]);

        self::assertSame(['title' => true, 'introText' => true], $result);
    }
}
