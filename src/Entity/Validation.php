<?php

namespace ORM\Entity;

use ORM\Dbal\Column;
use ORM\Dbal\Error;
use ORM\Dbal\Table;
use ORM\EntityManager as EM;
use ORM\Exception;

trait Validation
{
    /**
     * Check if the validator is enabled
     *
     * @return bool
     */
    public static function isValidatorEnabled()
    {
        return isset(self::$enabledValidators[static::class]) ?
            self::$enabledValidators[static::class] : static::$enableValidator;
    }

    /**
     * Enable validator
     *
     * @param bool $enable
     */
    public static function enableValidator($enable = true)
    {
        self::$enabledValidators[static::class] = $enable;
    }

    /**
     * Disable validator
     *
     * @param bool $disable
     */
    public static function disableValidator($disable = true)
    {
        self::$enabledValidators[static::class] = !$disable;
    }

    /**
     * Get a description for this table.
     *
     * @return Table|Column[]
     * @codeCoverageIgnore This is just a proxy
     */
    public static function describe()
    {
        return EM::getInstance(static::class)->describe(static::getTableName());
    }

    /**
     * Validate $value for $attribute
     *
     * @param string $attribute
     * @param mixed  $value
     * @return bool|Error
     * @throws Exception
     */
    public static function validate($attribute, $value)
    {
        return static::describe()->validate(static::getColumnName($attribute), $value);
    }

    /**
     * Validate $data
     *
     * $data has to be an array of $attribute => $value
     *
     * @param array $data
     * @return array
     */
    public static function validateArray(array $data)
    {
        $result = $data;
        foreach ($result as $attribute => &$value) {
            $value = static::validate($attribute, $value);
        }
        return $result;
    }

    /**
     * Check if the current data is valid
     *
     * Returns boolean true when valid otherwise an array of Errors.
     *
     * @return bool|Error[]
     */
    public function isValid()
    {
        $result = [];

        $presentColumns = [];
        foreach ($this->data as $column => $value) {
            $presentColumns[] = $column;
            $result[]         = static::validate($column, $value);
        }

        foreach (static::describe() as $column) {
            if (!in_array($column->name, $presentColumns)) {
                $result[] = static::validate($column->name, null);
            }
        }

        $result = array_values(array_filter($result, function ($error) {
            return $error instanceof Error;
        }));

        return count($result) === 0 ? true : $result;
    }
}
