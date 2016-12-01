<?php

namespace ORM;

use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidName;

/**
 * Abstract class of entity.
 *
 * The instance of an entity represents a row of the table.
 *
 * The class and statics describe the table.
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
abstract class Entity
{
    /** @var string */
    public static $tableNameTemplate = '%short%';

    /** @var string */
    public static $namingSchemeTable = 'snake_lower';

    /** @var string */
    public static $namingSchemeColumn = 'snake_lower';

    /** @var string */
    public static $namingSchemeMethods = 'camelCase';

    /** @var string */
    protected static $tableName;

    /** @var string[]|string */
    protected static $primaryKey = ['id'];

    /** @var string[] */
    protected static $columnAliases = [];

    /** @var string */
    protected static $columnPrefix;

    /** @var bool */
    protected static $autoIncrement = true;

    /** @var string */
    protected static $autoIncrementSequence;

    /** @var array */
    protected $data = [];

    // internal
    /** @var string[] */
    protected static $tableNames = [];
    /** @var string[][] */
    protected static $translatedColumns = [];
    /** @var \ReflectionClass[] */
    protected static $reflections = [];
    /** @var bool */
    protected $lifeCycleEnabled = false;

    /**
     * Get the table name.
     *
     * @return string
     * @throws InvalidName|InvalidConfiguration
     */
    public static function getTableName()
    {
        if (static::$tableName) {
            return static::$tableName;
        }

        if (!isset(self::$tableNames[static::class])) {
            $reflection = self::getReflection();

            $tableName = preg_replace_callback('/%([a-z]+)(\[(-?\d+)(\*)?\])?%/', function ($match) use ($reflection) {
                switch ($match[1]) {
                    case 'short':
                        $words = [$reflection->getShortName()];
                        break;

                    case 'namespace':
                        $words = explode('\\', $reflection->getNamespaceName());
                        break;

                    case 'name':
                        $words = preg_split('/[\\\\_]+/', $reflection->getName());
                        break;

                    default:
                        throw new InvalidConfiguration(
                            'Template invalid: Placeholder %' . $match[1] . '% is not allowed'
                        );
                }

                if (!isset($match[2])) {
                    return implode('_', $words);
                }
                $from = $match[3][0] === '-' ? count($words) - substr($match[3], 1) : $match[3];
                if (isset($words[$from])) {
                    return !isset($match[4]) ?
                        $words[$from] :
                        implode('_', array_slice($words, $from));
                }
                return '';
            }, static::$tableNameTemplate);

            if (empty($tableName)) {
                throw new InvalidName('Table name can not be empty');
            }
            self::$tableNames[static::class] = self::forceNamingScheme($tableName, static::$namingSchemeTable);
        }

        return self::$tableNames[static::class];
    }

    /**
     * Get the column name of the column $name.
     *
     * Important: getColumnName($name) === getColumnName(getColumnName($name))
     *
     * @param string $name
     * @return string
     */
    public static function getColumnName($name)
    {
        if (isset(static::$columnAliases[$name])) {
            return static::$columnAliases[$name];
        }

        if (!isset(self::$translatedColumns[static::class][$name])) {
            $colName = $name;

            if (static::$columnPrefix &&
                strpos($colName, self::forceNamingScheme(static::$columnPrefix, static::$namingSchemeColumn)) !== 0) {
                $colName = static::$columnPrefix . $colName;
            }

            self::$translatedColumns[static::class][$name] =
                self::forceNamingScheme($colName, static::$namingSchemeColumn);
        }

        return self::$translatedColumns[static::class][$name];
    }

    /**
     * Get the primary key for this Table
     *
     * @return array
     */
    public static function getPrimaryKey()
    {
        return !is_array(static::$primaryKey) ? [static::$primaryKey] : static::$primaryKey;
    }

    /**
     * Whether or not the table has an auto incremented primary key.
     *
     * @return bool
     */
    public static function isAutoIncremented()
    {
        return count(static::getPrimaryKey()) > 1 ? false : self::$autoIncrement;
    }

    /**
     * Get the sequence of the auto increment column (pgsql only).
     *
     * @return string
     */
    public static function getAutoIncrementSequence()
    {
        if (static::$autoIncrementSequence) {
            return static::$autoIncrementSequence;
        }
        return static::getTableName() . '_' . static::getColumnName(static::getPrimaryKey()[0]) . '_seq';
    }

    /**
     * Enforces $namingScheme to $name.
     *
     * @param string $name
     * @param string $namingScheme
     * @return string
     * @throws InvalidConfiguration
     */
    protected static function forceNamingScheme($name, $namingScheme)
    {
        $words = explode('_', preg_replace(
            '/([a-z0-9])([A-Z])/',
            '$1_$2',
            preg_replace_callback('/([a-z0-9])?([A-Z]+)([A-Z][a-z])/', function ($d) {
                return ($d[1] ? $d[1] . '_' : '') . $d[2] . '_' . $d[3];
            }, $name)
        ));

        switch ($namingScheme) {
            case 'snake_case':
                $newName = implode('_', $words);
                break;

            case 'snake_lower':
                $newName = implode('_', array_map('strtolower', $words));
                break;

            case 'SNAKE_UPPER':
                $newName = implode('_', array_map('strtoupper', $words));
                break;

            case 'Snake_Ucfirst':
                $newName = implode('_', array_map('ucfirst', $words));
                break;

            case 'camelCase':
                $newName = lcfirst(implode('', array_map('ucfirst', array_map('strtolower', $words))));
                break;

            case 'StudlyCaps':
                $newName = implode('', array_map('ucfirst', array_map('strtolower', $words)));
                break;

            case 'lower':
                $newName = implode('', array_map('strtolower', $words));
                break;

            case 'UPPER':
                $newName = implode('', array_map('strtoupper', $words));
                break;

            default:
                throw new InvalidConfiguration('Naming scheme ' . $namingScheme . ' unknown');
        }

        return $newName;
    }

    /**
     * Get reflection of the entity class.
     *
     * @return \ReflectionClass
     */
    protected static function getReflection()
    {
        if (!isset(self::$reflections[static::class])) {
            self::$reflections[static::class] = new \ReflectionClass(static::class);
        }
        return self::$reflections[static::class];
    }

    /**
     * Enables the life cycle.
     *
     * @param bool $enable
     * @internal
     */
    public function enableLifeCycle($enable = true)
    {
        $this->lifeCycleEnabled = $enable;
    }

    public function __set($var, $value)
    {
        $col = $this->getColumnName($var);

        $setter = self::forceNamingScheme('set' . ucfirst($var), static::$namingSchemeMethods);
        if (method_exists($this, $setter) && is_callable([$this, $setter])) {
            $oldValue = $this->__get($var);
            $md5OldData = md5(serialize($this->data));
            $this->$setter($value);
            $changed = $md5OldData !== md5(serialize($this->data));
        } else {
            $oldValue = $this->__get($var);
            $changed = @$this->data[$col] !== $value;
            $this->data[$col] = $value;
        }

        if ($this->lifeCycleEnabled && $changed) {
            $this->onChange($var, $oldValue, $this->__get($var));
        }
    }

    public function __get($var)
    {
        $getter = self::forceNamingScheme('get' . ucfirst($var), static::$namingSchemeMethods);
        if (method_exists($this, $getter) && is_callable([$this, $getter])) {
            return $this->$getter();
        } else {
            $col = static::getColumnName($var);
            return isset($this->data[$col]) ? $this->data[$col] : null;
        }
    }

    public function getRawData()
    {
        return $this->data;
    }


    public function onChange($var, $oldValue, $value)
    {
    }
}
