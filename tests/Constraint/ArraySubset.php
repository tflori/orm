<?php

namespace ORM\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

class ArraySubset extends Constraint
{
    /** @var array */
    protected $subset;

    /** @var bool */
    protected $strict;

    protected $delta = null;

    public function __construct($subset, bool $strict = false, float $delta = null)
    {
        $this->strict = $strict;
        $this->subset = $subset;
        $this->delta = $delta;
    }

    protected function matches($other): bool
    {
        return count($this->arrayDiffRecursive($this->subset, $other)) === 0;
    }

    public function toString(): string
    {
        return 'has the subset ' . $this->exporter()->export($this->subset);
    }

    protected function failureDescription($other): string
    {
        return 'Failed asserting that an array has the expected subset. Differences:' . PHP_EOL .
            json_encode($this->arrayDiffRecursive($this->subset, $other), JSON_PRETTY_PRINT);
    }

    public function arrayDiffRecursive(
        array $expected,
        array $actual,
        array &$difference = [],
        string $parentPath = ''
    ): array {
        $oldKey = 'expected';
        $newKey = 'actual';
        foreach ($expected as $k => $v) {
            $path = ($parentPath ? $parentPath . '.' : '') . $k;
            if (is_array($v)) {
                if (!array_key_exists($k, $actual) || !is_array($actual[$k])) {
                    $difference[$path][$oldKey] = $v;
                    $difference[$path][$newKey] = null;
                } else {
                    $this->arrayDiffRecursive($v, $actual[$k], $difference, $path);
                    if (!empty($recursion)) {
                        $difference[$oldKey][$k] = $recursion[$oldKey];
                        $difference[$newKey][$k] = $recursion[$newKey];
                    }
                }
            } else {
                if (!empty($v) && !array_key_exists($k, $actual)) {
                    $difference[$path][$oldKey] = $v;
                    $difference[$path][$newKey] = null;
                } else {
                    $a = $actual[$k] ?? null;
                    if ($this->delta && is_float($v)) {
                        $diff = abs($a - $v);
                        if ($diff > $this->delta) {
                            $difference[$path][$oldKey] = $v;
                            $difference[$path][$newKey] = $a;
                        }
                    } elseif ($this->strict && $a !== $v || $a != $v) {
                        $difference[$path][$oldKey] = $v;
                        $difference[$path][$newKey] = $a;
                    }
                }
            }
        }
        return $difference;
    }
}
