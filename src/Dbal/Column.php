<?php

namespace ORM\Dbal;

/**
 * Describes a column of a database table
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 *
 * @property string name
 * @property type Type
 * @property mixed default
 * @property bool nullable
 */
class Column
{
    /** @var string[] */
    protected static $registeredTypes = [];

    /**
     * Register $type for describe
     *
     * @param string $type The full qualified class name
     */
    public static function registerType($type)
    {
        if (!in_array($type, static::$registeredTypes)) {
            array_unshift(static::$registeredTypes, $type);
        }
    }

    /** @var array */
    protected $columnDefinition;

    /** @var Dbal */
    protected $dbal;

    /** @var TypeInterface */
    protected $type;

    /** @var bool */
    protected $hasDefault;

    /** @var bool */
    protected $isNullable;

    /**
     * Column constructor.
     *
     * @param Dbal  $dbal
     * @param array $columnDefinition
     */
    public function __construct(Dbal $dbal, array $columnDefinition)
    {
        $this->dbal = $dbal;
        $this->columnDefinition = $columnDefinition;
    }

    public function validate($value)
    {
        if ($value === null) {
            if ($this->nullable || $this->hasDefault()) {
                return true;
            }

            return new Error\NotNullable($this);
        }

        return $this->getType()->validate($value);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'name':
                return $this->columnDefinition['column_name'];
            case 'type':
                return $this->getType();
            case 'default':
                return $this->columnDefinition['column_default'];
            case 'nullable':
                return $this->columnDefinition['is_nullable'] === true ||
                       $this->columnDefinition['is_nullable'] === 'YES';
            default:
                return isset($this->columnDefinition[$name]) ? $this->columnDefinition[$name] : null;
        }
    }

    public function hasDefault()
    {
        return $this->default !== null;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        if (!$this->type) {
            $class = null;

            if (isset($this->columnDefinition['type']) && class_exists($this->columnDefinition['type'])) {
                $class = $this->columnDefinition['type'];
            }

            if (!$class) {
                foreach (self::$registeredTypes as $c) {
                    if (call_user_func([$c, 'fits'], $this->columnDefinition)) {
                        $class = $c;
                    }
                }
                $class = $class ?: Type\Text::class;
            }

            $this->type = call_user_func([$class, 'factory'], $this->dbal, $this->columnDefinition);
        }

        return $this->type;
    }
}
