<?php

namespace ORM\Relation;

trait HasReference
{
    /** Reference definition as key value pairs
     * @var array */
    protected $reference;

    public function getReference()
    {
        return $this->reference;
    }
}
