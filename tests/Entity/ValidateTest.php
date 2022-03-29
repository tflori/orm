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
    protected function setUp(): void
    {
        parent::setUp();

        $this->mocks['table'] = \Mockery::mock(Table::class);
        $this->mocks['em']->shouldReceive('describe')->with('article')->andReturn($this->mocks['table'])->byDefault();
    }

    /** @test */
    public function getsTableFromEmDescribe()
    {
        $this->mocks['em']->shouldReceive('describe')->with('article')->once()->andReturn($this->mocks['table']);

        Article::describe();
    }

    /** @test */
    public function validateUsesTableFromDescribe()
    {
        $this->mocks['table']->shouldReceive('validate')->with('title', 'Hello World!')->once()->andReturn(true);

        Article::validate('title', 'Hello World!');
    }

    /** @test */
    public function convertsFieldNamesToColumns()
    {
        $this->mocks['table']->shouldReceive('validate')->with('intro_text', 'This is just a test article.')
            ->once()->andReturn(true);

        Article::validate('introText', 'This is just a test article.');
    }

    /** @test */
    public function validateArray()
    {
        $this->mocks['table']->shouldReceive('validate')->with('title', 'Hello World!')->once()->andReturn(true);
        $this->mocks['table']->shouldReceive('validate')->with('intro_text', 'This is just a test article.')
            ->once()->andReturn(true);

        $result = Article::validateArray([
            'title' => 'Hello World!',
            'introText' => 'This is just a test article.',
        ]);

        self::assertSame(['title' => true, 'introText' => true], $result);
    }

    /** @test */
    public function byDefaultValidatorIsDisabled()
    {
        self::assertFalse(Article::isValidatorEnabled());
    }

    /**
     * @depends testByDefaultValidatorIsDisabled
     */
    /** @test */
    public function validatorCanBeEnabled()
    {
        Article::enableValidator();

        self::assertTrue(Article::isValidatorEnabled());
    }

    /**
     * @depends testValidatorCanBeEnabled
     */
    /** @test */
    public function validatorCanBeDisabled()
    {
        Article::disableValidator();

        self::assertFalse(Article::isValidatorEnabled());
    }

    /** @test */
    public function validatorCanBeActivatedByStatic()
    {
        self::assertTrue(Category::isValidatorEnabled());
    }
}
