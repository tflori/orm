<?php

namespace ORM\Test;

use ORM\Exceptions\Base;

class ExceptionsTest extends TestCase
{
    public function provideExceptionClasses()
    {
        exec('find "' . __DIR__ . '/../src" -type f -iwholename "*exception*.php"', $exceptionFiles);
        $exceptionClasses = [];
        foreach ($exceptionFiles as $exceptionFile) {
            $exceptionClasses[] = 'ORM\\' . str_replace(
                ['/', '.php'],
                ['\\', ''],
                substr($exceptionFile, strlen(__DIR__ . '/../src/'))
            );
        }
        return array_map(function ($exceptionClass) {
            return [$exceptionClass];
        }, $exceptionClasses);
    }

    /**
     * lets see if every exception is really a \Exception
     *
     * @dataProvider provideExceptionClasses
     */
    public function testExceptionsAreExceptions($exceptionClass)
    {
        $exception = new $exceptionClass('No exception');

        self::assertInstanceOf(\Exception::class, $exception);
    }

    /**
     * les see if every exception extends Base for easier handling
     *
     * You can just catch every ORM\Exception\Base if you need to.
     *
     * @dataProvider provideExceptionClasses
     */
    public function testExceptionsExtendBase($exceptionClass)
    {
        $exception = new $exceptionClass('No exception');

        self::assertInstanceOf(Base::class, $exception);
    }
}
