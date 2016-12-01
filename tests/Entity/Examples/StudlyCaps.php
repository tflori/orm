<?php

namespace ORM\Test\Entity\Examples;

class StudlyCaps extends TestEntity
{
    protected $anotherVar = 'foobaz';

    public function onChange($var, $oldValue, $value)
    {
    }

    public function setAnotherVar($value)
    {
        $this->anotherVar = $value;
    }

    public function getAnotherVar()
    {
        return $this->anotherVar;
    }
}
