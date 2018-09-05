<?php

namespace ORM\Test\Entity\Examples;

use ORM\Entity;

class GeneratesUuid extends Entity implements Entity\GeneratesPrimaryKeys
{
    protected static $autoIncrement = false;

    protected function generatePrimaryKey()
    {
        $this->id = uniqid();
    }
}
