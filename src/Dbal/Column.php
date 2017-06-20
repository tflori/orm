<?php

namespace ORM\Dbal;

use ORM\Dbal\Error\NotValid;

/**
 * Describes a column of a database table
 *
 * @package ORM\Dbal
 * @author  Thomas Flori <thflori@gmail.com>
 *
 * @property string name
 * @property Type   type
 * @property mixed  default
 * @property bool   nullable
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

    /**
     * Get the registered type for $columnDefinition
     *
     * @param array $columnDefinition
     * @return string
     */
    protected static function getRegisteredType(array $columnDefinition)
    {
        foreach (self::$registeredTypes as $class) {
            if (call_user_func([ $class, 'fits' ], $columnDefinition)) {
                return $class;
            }
        }

        return null;
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
        $this->dbal             = $dbal;
        $this->columnDefinition = $columnDefinition;
    }

    /**
     * Check if $value is valid for this type
     *
     * @param mixed $value
     * @return boolean|Error
     */
    public function validate($value)
    {
        if ($value === null) {
            if ($this->nullable || $this->hasDefault()) {
                return true;
            }

            return new Error\NotNullable($this);
        }

        $valid = $this->getType()->validate($value);

        if ($valid === false) {
            return new NotValid($this, new Error());
        }

        if ($valid instanceof Error) {
            return new NotValid($this, $valid);
        }

        return true;
    }

    /**
     * Get attributes from column
     *
     * @param string $name
     * @return mixed
     */
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

    /**
     * Check if default value is given
     *
     * @return bool
     */
    public function hasDefault()
    {
        return $this->default !== null;
    }

    /**
     * Determine and return the type
     *
     * @return Type
     */
    public function getType()
    {
        if (!$this->type) {
            if (!isset($this->columnDefinition['type'])) {
                $class = self::getRegisteredType($this->columnDefinition);
            } else {
                $class = $this->columnDefinition['type'];
            }

            if ($class === null || !is_callable([ $class, 'factory' ])) {
                $class = Type\Text::class;
            }

            $this->type = call_user_func([ $class, 'factory' ], $this->dbal, $this->columnDefinition);
        }

        return $this->type;
    }
}
