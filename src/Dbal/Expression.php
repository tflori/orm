<?php

namespace ORM\Dbal;

class Expression
{
    protected $expression = '';

    /**
     * Expression constructor.
     *
     * @param string $expression
     */
    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function __toString()
    {
        return $this->expression;
    }
}
