<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class VarChar extends Type
{
    protected static $dataTypes = [
        'varchar',
        'char',
        'character varying',
        'character',
    ];

    protected $maxLength;

    /**
     * VarChar constructor.
     *
     * @param int $maxLength
     */
    public function __construct($maxLength = null)
    {
        $this->maxLength = $maxLength;
    }
}
