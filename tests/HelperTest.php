<?php

namespace ORM\Test;

use Mockery as m;
use ORM\Entity;
use ORM\Exception\InvalidArgument;
use ORM\Helper;
use ORM\QueryBuilder\Parenthesis;
use ORM\QueryBuilder\QueryBuilder;
use ORM\Test\Entity\Examples\Article;
use ORM\Test\Entity\Examples\Concerns\WithCreated;
use ORM\Test\Entity\Examples\Concerns\WithUpdated;

class HelperTest extends TestCase
{
    /** @test */
    public function returnsTheShortNameOfAClass()
    {
        $result = Helper::shortName(static::class);

        self::assertSame('HelperTest', $result);
    }

    /** @test */
    public function returnsAllTraitsUsed()
    {
        $result = Helper::traitUsesRecursive(Entity::class);

        self::assertSame(class_uses(Entity::class), $result);
    }

    /** @test */
    public function returnsTraitsFromParentClass()
    {
        $result = Helper::traitUsesRecursive(QueryBuilder::class);

        foreach (class_uses(Parenthesis::class) as $trait) {
            self::assertArrayHasKey($trait, $result);
            self::assertSame($trait, $result[$trait]);
        }
    }

    /** @test */
    public function returnsTraitsFromTraits()
    {
        $result = Helper::traitUsesRecursive(Article::class);

        self::assertArrayHasKey(WithCreated::class, $result);
        self::assertArrayHasKey(WithUpdated::class, $result);
    }

    /** @test */
    public function returnsDefaultWhenTheArrayIsEmpty()
    {
        $result = Helper::first([], 'foo bar');

        self::assertSame('foo bar', $result);
    }

    /** @test */
    public function pluckUsesTheCallbackToGetAValue()
    {
        $array = [[1,2],[3,4]];

        $result = Helper::pluck($array, function ($item) {
            return max($item);
        });

        self::assertSame([2,4], $result);
    }

    /** @test */
    public function pluckUsesTheKeyOfAssocArrays()
    {
        $array = [['foo' => 1, 'bar' => 2],['foo' => 3, 'bar' => 4]];

        $foos = Helper::pluck($array, 'foo');
        $bars = Helper::pluck($array, 'bar');

        self::assertSame([1,3], $foos);
        self::assertSame([2,4], $bars);
    }

    /** @test */
    public function pluckUsesThePublicPropertiesOfObjects()
    {
        $array = [(object)['foo' => 1, 'bar' => 2],(object)['foo' => 3, 'bar' => 4]];

        $foos = Helper::pluck($array, 'foo');
        $bars = Helper::pluck($array, 'bar');

        self::assertSame([1,3], $foos);
        self::assertSame([2,4], $bars);
    }

    /** @test */
    public function pluckUsesTheMagicGetterAndIsset()
    {
        $array = [
            new Article(['foo' => 42]),
            new Article(['foo' => 42]),
        ];

        $foos = Helper::pluck($array, 'foo');

        self::assertSame([42, 42], $foos);
    }

    /** @test */
    public function pluckReturnsNullIfTheKeyIsNotAvailable()
    {
        $array = [['bar' => 42],['bar' => 42]];

        $foos = Helper::pluck($array, 'foo');

        self::assertSame([null, null], $foos);
    }

    /** @test */
    public function keyByCreatesAnArrayWithValueFromRetrieverAsKey()
    {
        $array = [['foo' => 'a', 'bar' => 'b'], ['foo' => 'c', 'bar' => 'd']];

        $result = Helper::keyBy($array, 'foo');

        self::assertSame([
            'a' => ['foo' => 'a', 'bar' => 'b'],
            'c' => ['foo' => 'c', 'bar' => 'd']
        ], $result);
    }

    /** @test */
    public function keyByAnArrayCreatesCombinedKeysFromTheValues()
    {
        $array = [['foo' => 'a', 'bar' => 'b'], ['bar' => 'c', 'foo' => 'd']];

        $result = Helper::keyBy($array, ['foo', 'bar']);

        self::assertSame([
            'a-b' => ['foo' => 'a', 'bar' => 'b'],
            'd-c' => ['bar' => 'c', 'foo' => 'd']
        ], $result);
    }
    
    /** @test */
    public function groupByCreatesGroupsWithKeysFromTheValue()
    {
        $array = [['foo' => 'a', 'bar' => 'b'], ['bar' => 'c', 'foo' => 'a']];

        $result = Helper::groupBy($array, 'foo');

        self::assertSame([
            'a' => [['foo' => 'a', 'bar' => 'b'], ['bar' => 'c', 'foo' => 'a']],
        ], $result);
    }

    /** @test */
    public function keyByPluckAndGroupByDontAllowOnlyStringCallableOrArray()
    {
        $array = [['a', 'b', 'c'], ['d', 'e', 'f']];

        self::expectException(InvalidArgument::class);

        Helper::keyBy($array, 1);
    }

    /** @test */
    public function onlyReturnsOnlyItemsWithKeys()
    {
        $array = ['a' => 'foo', 'b' => 'bar', 'c' => 'baz'];

        $result = Helper::only($array, ['a', 'c']);

        self::assertSame(['a' => 'foo', 'c' => 'baz'], $result);
    }
}
