<?php

namespace ORM\Dbal\Type;

use ORM\Dbal\Dbal;
use ORM\Dbal\Type;

/**
 * String data type
 *
 * With and without max / fixed length
 *
 * @package ORM\Dbal\Type
 * @author  Thomas Flori <thflori@gmail.com>
 */
class VarChar extends Type
{
    /** @var int */
    protected $maxLength;

    /**
     * VarChar constructor.
     *
     * @param int $maxLength
     */
    public function __construct($maxLength = null)
    {
        $this->maxLength = (int)$maxLength;
    }

    public static function factory(Dbal $dbal, array $columnDefinition)
    {
        return new static($columnDefinition['character_maximum_length']);
    }

    public function validate($value)
    {
        // TODO: Implement validate() method.
    }
}
