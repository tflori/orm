<?php

namespace ORM\Test\Dbal\Validator;

use Mockery\Mock;
use ORM\Dbal\Column;
use ORM\Dbal\Type\Boolean;
use ORM\Dbal\Type\Number;
use ORM\Dbal\Type\VarChar;
use ORM\Exception;
use ORM\Test\TestCase;
use ORM\Dbal\Validator;

class ValidateTest extends TestCase
{
//    public function testDescribeGetsCalledOnce()
//    {
//        $this->em->shouldReceive('describe')->once()->with('db.table')
//            ->andReturn([
//                new Column('id', new Integer(), true, false),
//                new Column('name', new VarChar(10), false, false),
//            ]);
//        $validator = new Validator('db.table', $this->em);
//
//        $validator->validate('id', 23);
//        $validator->validate('name', 'Test');
//    }

    public function testThrowsWhenColumnDoesNotExist()
    {
        $validator = new Validator([]);

        self::expectException(Exception::class);
        self::expectExceptionMessage('Unknown column id');

        $validator->validate('id', 23);
    }

    public function testAllowsNullValues()
    {
        /** @var Mock|Integer $type */
        $type = \Mockery::mock(Number::class);
        $validator = new Validator([
            new Column('colA', $type, false, true)
        ]);
        $type->shouldNotReceive('validate');

        $result = $validator->validate('colA', null);

        self::assertSame(true, $result);
    }

    public function testAllowsNullWithDefault()
    {
        /** @var Mock|Integer $type */
        $type = \Mockery::mock(Number::class);
        $validator = new Validator([
            new Column('colA', $type, true, false)
        ]);
        $type->shouldNotReceive('validate');

        $result = $validator->validate('colA', null);

        self::assertSame(true, $result);
    }

    public function testReturnsNotNullable()
    {
        /** @var Mock|Integer $type */
        $type = \Mockery::mock(Number::class);
        $validator = new Validator([
            new Column('colA', $type, false, false)
        ]);
        $type->shouldNotReceive('validate');

        $result = $validator->validate('colA', null);

        self::assertInstanceOf(Validator\Error\NotNullable::class, $result);
    }

    public function testValidatesUsingType()
    {
        /** @var Mock|Boolean $type */
        $type = \Mockery::mock(Boolean::class);
        $validator = new Validator([
            new Column('colA', $type, true, false)
        ]);

        $type->shouldReceive('validate')->with(true)->once()->andReturn(true);

        $result = $validator->validate('colA', true);

        self::assertSame(true, $result);
    }

    public function testReturnsNotValid()
    {
        /** @var Mock|Boolean $type */
        $type = \Mockery::mock(Boolean::class);
        $validator = new Validator([
            new Column('colA', $type, true, false)
        ]);

        $type->shouldReceive('validate')->with('y')->once()->andReturn(false);

        $result = $validator->validate('colA', 'y');

        self::assertInstanceOf(Validator\Error\NotValid::class, $result);
    }
}
