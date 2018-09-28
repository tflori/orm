<?php

namespace ORM\Test;

class ClosureWrapper
{
    private $closure;
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }
    public function __invoke()
    {
        return call_user_func_array($this->closure, func_get_args());
    }
}
