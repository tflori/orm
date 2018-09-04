<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class GeneratesUuid extends Entity
{
    protected static $autoIncrement = false;

    protected function generatePrimaryKey()
    {
        $this->id = uniqid();
    }
}
