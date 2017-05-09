<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Type;

class Text extends Type
{
    protected static $dataTypes = [
        'text',
        'tinytext',
        'mediumtext',
        'longtext',
    ];
}
