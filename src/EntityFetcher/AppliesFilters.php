<?php

namespace ORM\EntityFetcher;

use ORM\Exception\InvalidArgument;

trait AppliesFilters
{
    /** Filters that always should be applied for an entity
     * @var FilterInterface[][] */
    protected static $globalFilters = [];

    /** A list of filters that should not be applied for this fetcher
     * @var string[] */
    protected $excludedFilters = [];

    /** A list of filters to apply additionally
     * @var FilterInterface[] */
    protected $filters = [];

    /** Boolean if the filters where applied
     * @var bool */
    protected $filtersApplied = false;

    /**
     * Register $filter globally for $class
     *
     * A registered filter will be applied in all entity fetchers for the class if not excluded by
     * `$fetcher->withoutFilter(Filter::class)`.
     *
     * @param $class
     * @param FilterInterface|callable $filter
     */
    public static function registerFilterGlobally($class, $filter)
    {
        if (isset(static::$globalFilters[$class])) {
            static::$globalFilters[$class] = [];
        }

        static::$globalFilters[$class][] = static::normalizeFilter($filter);
    }

    /**
     * Exclude $filterClass for this fetcher
     *
     * @param string $filterClass
     * @return $this
     */
    public function withoutFilter($filterClass)
    {
        $this->excludedFilters[] = $filterClass;
        return $this;
    }

    /**
     * Apply an additional $filter before executing
     *
     * @param FilterInterface|callable $filter
     * @return $this
     */
    public function filter($filter)
    {
        $this->filters[] = static::normalizeFilter($filter);
        return $this;
    }

    /**
     * Apply the filters on $this
     */
    protected function applyFilters()
    {
        if ($this->filtersApplied) {
            return;
        }

        $globalFilters = isset(static::$globalFilters[$this->class]) ? static::$globalFilters[$this->class] : [];
        foreach ($globalFilters as $filter) {
            if (in_array(get_class($filter), $this->excludedFilters)) {
                continue;
            }
            $filter->apply($this);
        }
        foreach ($this->filters as $filter) {
            $filter->apply($this);
        }
    }

    /**
     * Converts callables into a CallableFilter
     *
     * @param FilterInterface|callable $filter
     * @return CallableFilter|FilterInterface
     * @throws InvalidArgument
     */
    protected static function normalizeFilter($filter)
    {
        if (is_callable($filter)) {
            $filter = new CallableFilter($filter);
        }

        if (!$filter instanceof FilterInterface) {
            throw new InvalidArgument('Argument 1 for ' . __METHOD__ . ' should be an instance of FilterInterface');
        }

        return $filter;
    }
}
