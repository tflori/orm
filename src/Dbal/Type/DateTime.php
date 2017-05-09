<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class DateTime extends Type
{
    protected static $dataTypes = [
        'date',
        'datetime',
        'timestamp',
    ];
}
