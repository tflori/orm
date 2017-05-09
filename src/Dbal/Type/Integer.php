<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class Integer extends Type
{
    protected static $dataTypes = [
        'serial',
        'bigserial',
        'smallint',
        'integer',
        'bigint',
        'tinyint',
        'mediumint',
        'int',
    ];
}
