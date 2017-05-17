<?php

namespace ORM\Dbal;

class Column
{
    /** @var string */
    protected $name;

    /** @var TypeInterface */
    protected $type;

    /** @var bool */
    protected $hasDefault;

    /** @var bool */
    protected $isNullable;

    /**
     * Column constructor.
     *
     * @param string $name
     * @param TypeInterface $type
     * @param bool $hasDefault
     * @param bool $isNullable
     */
    public function __construct($name, TypeInterface $type, $hasDefault, $isNullable)
    {
        $this->name = $name;
        $this->type = $type;
        $this->hasDefault = $hasDefault;
        $this->isNullable = $isNullable;
    }

    public static function factory($columnDefinition, $type)
    {
        $name = $columnDefinition['column_name'];
        $hasDefault = $columnDefinition['column_default'] !== null;
        $isNullable = $columnDefinition['is_nullable'];
        return new static($name, $type, $hasDefault, $isNullable);
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
