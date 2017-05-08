<?php

namespace ORM\Dbal;

class Column
{
    /** @var string */
    protected $name;

    /** @var Type */
    protected $type;

    /** @var bool */
    protected $hasDefault;

    /** @var bool */
    protected $isNullable;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param Type   $type
     * @param bool   $hasDefault
     * @param bool   $isNullable
     */
    public function __construct($name, Type $type, $hasDefault, $isNullable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->hasDefault = $hasDefault;
        $this->isNullable = $isNullable;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->isNullable;
    }
}
