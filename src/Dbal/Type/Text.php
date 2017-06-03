<?php

namespace ORM\Dbal\Type;

/**
 * Text data type
 *
 * This is also the base type for any other data type
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Text extends VarChar
{
    protected $type = 'text';
}
