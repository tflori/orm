<?php

namespace ORM\Test;

use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_Warning;
use PHPUnit_TextUI_ResultPrinter;

class Printer extends PHPUnit_TextUI_ResultPrinter
{

    /**
     * Replacement symbols for test statuses.
     *
     * @var array
     */
    protected static $symbols = [
        'E' => "\e[31m!\e[0m", // red !
        'F' => "\e[31m\xe2\x9c\x96\e[0m", // red X
        'W' => "\e[33mW\e[0m", // yellow W
        'I' => "\e[33mI\e[0m", // yellow I
        'R' => "\e[33mR\e[0m", // yellow R
        'S' => "\e[36mS\e[0m", // cyan S
        '.' => "\e[32m\xe2\x9c\x94\e[0m", // green checkmark
    ];
    /**
     * Structure of the outputted test row.
     *
     * @var string
     */
    protected $testRow = '';

    /** @var string */
    protected $previousClassName = '';

    /**
     * {@inheritdoc}
     */
    protected function writeProgress($progress)
    {
        if ($this->hasReplacementSymbol($progress)) {
            $progress = static::$symbols[$progress];
        }
        $this->write("  {$progress} {$this->testRow}" . PHP_EOL);
        $this->column++;
        $this->numTestsRun++;
    }
    /**
     * {@inheritdoc}
     */
    public function addError(PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-red');
        parent::addError($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-red');
        parent::addFailure($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function addWarning(PHPUnit_Framework_Test $test, PHPUnit_Framework_Warning $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-yellow');
        parent::addWarning($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-yellow');
        parent::addIncompleteTest($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function addRiskyTest(PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-yellow');
        parent::addRiskyTest($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->buildTestRow(get_class($test), $test->getName(), $time, $test->getNumAssertions(), 'fg-cyan');
        parent::addSkippedTest($test, $e, $time);
    }
    /**
     * {@inheritdoc}
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        $testName = \PHPUnit_Util_Test::describe($test);
        if ($this->hasCompoundClassName($testName)) {
            list($className, $methodName) = explode('::', $testName);
            $this->buildTestRow($className, $methodName, $time, $test->getNumAssertions());
        }
        parent::endTest($test, $time);
    }
    /**
     * {@inheritdoc}
     *
     * We'll handle the coloring ourselves.
     */
    protected function writeProgressWithColor($color, $buffer)
    {
        return $this->writeProgress($buffer);
    }

    /**
     * Formats the results for a single test.
     *
     * @param        $className
     * @param        $methodName
     * @param        $time
     * @param int    $count
     * @param string $color
     */
    protected function buildTestRow($className, $methodName, $time, $count = 0, $color = 'fg-white')
    {
        if ($className != $this->previousClassName) {
            $this->write(PHP_EOL . $this->formatWithColor('fg-magenta', $className) . PHP_EOL);
            $this->previousClassName = $className;
        }

        $this->testRow = sprintf(
            "(%s, %s) %s",
            $this->formatTestDuration($time),
            $this->formatAssertionCount($count),
            $this->formatWithColor($color, $this->formatMethodName($methodName))
        );
    }
    /**
     * Makes the method name more readable.
     *
     * @param $method
     * @return mixed
     */
    protected function formatMethodName($method)
    {
        return ucfirst(
            $this->splitCamels(
                $this->splitSnakes($method)
            )
        );
    }
    /**
     * Replaces underscores in snake case with spaces.
     *
     * @param $name
     * @return string
     */
    protected function splitSnakes($name)
    {
        return str_replace('_', ' ', $name);
    }
    /**
     * Splits camel-cased names while handling caps sections properly.
     *
     * @param $name
     * @return string
     */
    protected function splitCamels($name)
    {
        return preg_replace('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', ' $1', $name);
    }
    /**
     * Colours the duration if the test took longer than 500ms.
     *
     * @param $time
     * @return string
     */
    protected function formatTestDuration($time)
    {
        $text = sprintf('%d ms', round($time * 1000));
        return $time > 0.5 ? $this->formatWithColor('fg-yellow', $text) : $text;
    }

    /**
     * Colours the assertion if the test has 0 assertions.
     *
     * @param $count
     * @return string
     */
    protected function formatAssertionCount($count)
    {
        $text = sprintf('%d assertions', $count);
        return $count == 0 ? $this->formatWithColor('fg-red', $text) : $text;
    }

    /**
     * Verifies if we have a replacement symbol available.
     *
     * @param $progress
     * @return bool
     */
    protected function hasReplacementSymbol($progress)
    {
        return in_array($progress, array_keys(static::$symbols));
    }
    /**
     * Checks if the class name is in format Class::method.
     *
     * @param $testName
     * @return bool
     */
    protected function hasCompoundClassName($testName)
    {
        return ! empty($testName) && strpos($testName, '::') > -1;
    }
}
