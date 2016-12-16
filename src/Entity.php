<?php

namespace ORM;

use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidName;

/**
 * Definition of an entity
 *
 * The instance of an entity represents a row of the table and the statics variables and methods describe the database
 * table.
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
abstract class Entity
{
    /** The template to use to calculate the table name.
     * @var string */
    public static $tableNameTemplate = '%short%';

    /** The naming scheme to use for table names.
     * @var string */
    public static $namingSchemeTable = 'snake_lower';

    /** The naming scheme to use for column names.
     * @var string */
    public static $namingSchemeColumn = 'snake_lower';

    /** The naming scheme to use for method names.
     * @var string */
    public static $namingSchemeMethods = 'camelCase';

    /** The database connection to use.
     * @var string */
    public static $connection = 'default';

    /** Fixed table name (ignore other settings)
     * @var string */
    protected static $tableName;

    /** The variable(s) used for primary key.
     * @var string[]|string */
    protected static $primaryKey = ['id'];

    /** Fixed column names (ignore other settings)
     * @var string[] */
    protected static $columnAliases = [];

    /** A prefix for column names.
     * @var string */
    protected static $columnPrefix;

    /** Whether or not the primary key is auto incremented.
     * @var bool */
    protected static $autoIncrement = true;

    /** Auto increment sequence to use for pgsql.
     * @var string */
    protected static $autoIncrementSequence;

    // data
    /** The current data of a row.
     * @var mixed[] */
    protected $data = [];

    /** The original data of the row.
     * @var mixed[] */
    protected $originalData = [];

    // internal
    /** Calculated table names.
     * @internal
     * @var string[] */
    protected static $calculatedTableNames = [];

    /** Calculated column names.
     * @internal
     * @var string[][] */
    protected static $calculatedColumnNames = [];

    /** The reflections of the classes.
     * @internal
     * @var \ReflectionClass[] */
    protected static $reflections = [];

    /**
     * Get the table name
     *
     * The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
     * $tableName.
     *
     * @return string
     * @throws InvalidName|InvalidConfiguration
     */
    public static function getTableName()
    {
        if (static::$tableName) {
            return static::$tableName;
        }

        if (!isset(self::$calculatedTableNames[static::class])) {
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
            self::$calculatedTableNames[static::class] =
                self::forceNamingScheme($tableName, static::$namingSchemeTable);
        }

        return self::$calculatedTableNames[static::class];
    }

    /**
     * Get the column name of $name
     *
     * The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
     * to $namingSchemeColumn.
     *
     * **ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to exactly
     * the same as getColumnName($name).
     *
     * @link https://tflori.github.io/orm/entityDefinition.html
     * @param string $var
     * @return string
     */
    public static function getColumnName($var)
    {
        if (isset(static::$columnAliases[$var])) {
            return static::$columnAliases[$var];
        }

        if (!isset(self::$calculatedColumnNames[static::class][$var])) {
            $colName = $var;

            if (static::$columnPrefix &&
                strpos($colName, self::forceNamingScheme(static::$columnPrefix, static::$namingSchemeColumn)) !== 0) {
                $colName = static::$columnPrefix . $colName;
            }

            self::$calculatedColumnNames[static::class][$var] =
                self::forceNamingScheme($colName, static::$namingSchemeColumn);
        }

        return self::$calculatedColumnNames[static::class][$var];
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
     * Entity constructor.
     *
     * @param array $data
     * @param bool $fromDatabase
     */
    final public function __construct(array $data = [], $fromDatabase = false)
    {
        $this->data = $this->originalData = array_merge($this->data, $data);
        $this->onInit(!$fromDatabase);
    }

    /**
     * Magic setter.
     *
     * You can overwrite this for custom functionality but we recommend not to use the properties or setter (set*)
     * directly when they have to update the data stored in table.
     *
     * @param $var
     * @param $value
     */
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

        if ($changed) {
            $this->onChange($var, $oldValue, $this->__get($var));
        }
    }

    /**
     * Magic getter.
     *
     * @param $var
     * @return mixed|null
     */
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

    /**
     * Checks if entity or $var got changed.
     *
     * @param string $var
     * @return bool
     */
    public function isDirty($var = null)
    {
        if ($var) {
            $col = static::getColumnName($var);
            return @$this->data[$col] !== @$this->originalData[$col];
        }

        return md5(serialize($this->data)) !== md5(serialize($this->originalData));
    }

    /**
     * Resets the entity or $var to original data.
     *
     * @param string $var
     */
    public function reset($var = null)
    {
        if ($var) {
            $col = static::getColumnName($var);
            if (isset($this->originalData[$col])) {
                $this->data[$col] = $this->originalData[$col];
            } else {
                unset($this->data[$col]);
            }
            return;
        }

        $this->data = $this->originalData;
    }

    /**
     * Save the entity to $entityManager.
     *
     * @param EntityManager $entityManager
     */
    public function save(EntityManager $entityManager)
    {
        if ($this->isDirty()) {
            $entityManager->save($this, $this->data);
        }
    }

    /**
     * Set new original data.
     *
     * @param array $data
     * @internal
     */
    final public function setOriginalData(array $data)
    {
        $this->originalData = $data;
    }

    /**
     * Empty event handler.
     *
     * Get called when something is changed with magic setter.
     *
     * @param string $var
     * @param mixed  $oldValue
     * @param mixed  $value
     */
    public function onChange($var, $oldValue, $value)
    {
    }

    /**
     * Empty event handler.
     *
     * Get called when the entity get initialized.
     *
     * @param bool $new
     */
    public function onInit($new)
    {
    }
}
