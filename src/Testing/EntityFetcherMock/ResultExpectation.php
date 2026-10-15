<?php

namespace ORM\Testing\EntityFetcherMock;

use Mockery\Expectation;

/**
 * Expectation that calls the real method of the Result until a return value is defined
 *
 * Mockery ignores andReturn*() and andThrow() when passthru() is set - this expectation disables passthru() when a
 * return value is defined. All other and*() methods use andReturn() internally.
 *
 * @internal
 */
class ResultExpectation extends Expectation
{
    /** {@inheritDoc} */
    public function andReturn(...$args)
    {
        $this->_passthru = false;
        return parent::andReturn(...$args);
    }

    /** {@inheritDoc} */
    public function andReturnUsing(...$args)
    {
        $this->_passthru = false;
        return parent::andReturnUsing(...$args);
    }

    /** {@inheritDoc} */
    public function andReturnArg($index)
    {
        $this->_passthru = false;
        return parent::andReturnArg($index);
    }

    /** {@inheritDoc} */
    public function andYield(...$args)
    {
        $this->_passthru = false;
        return parent::andYield(...$args);
    }
}
