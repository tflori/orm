<?php

namespace ORM;

use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidName;
use ORM\Exceptions\NoEntityManager;
use ORM\Exceptions\UndefinedRelation;

/**
 * Definition of an entity
 *
 * The instance of an entity represents a row of the table and the statics variables and methods describe the database
 * table.
 *
 * This is the main part where your configuration efforts go. The following properties and methods are well documented
 * in the manual under [https://tflori.github.io/orm/entityDefinition.html](Entity Definition).
 *
 * @package ORM
 * @link https://tflori.github.io/orm/entityDefinition.html Entity Definition
 * @author Thomas Flori <thflori@gmail.com>
 */
abstract class Entity implements \Serializable
{
    const OPT_RELATION_CLASS       = 'class';
    const OPT_RELATION_CARDINALITY = 'cardinality';
    const OPT_RELATION_REFERENCE   = 'reference';
    const OPT_RELATION_OPPONENT    = 'opponent';
    const OPT_RELATION_TABLE       = 'table';

    /** The template to use to calculate the table name.
     * @var string */
    protected static $tableNameTemplate = '%short%';

    /** The naming scheme to use for table names.
     * @var string */
    protected static $namingSchemeTable = 'snake_lower';

    /** The naming scheme to use for column names.
     * @var string */
    protected static $namingSchemeColumn = 'snake_lower';

    /** The naming scheme to use for method names.
     * @var string */
    protected static $namingSchemeMethods = 'camelCase';

    /** Whether or not the naming got used
     * @var bool */
    protected static $namingUsed = false;

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

    /** Relation definitions
     * @var array */
    protected static $relations = [];

    /** The current data of a row.
     * @var mixed[] */
    protected $data = [];

    /** The original data of the row.
     * @var mixed[] */
    protected $originalData = [];

    /** The entity manager from which this entity got created
     * @var EntityManager*/
    protected $entityManager;

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
            static::$namingUsed = true;
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
            }, static::getTableNameTemplate());

            if (empty($tableName)) {
                throw new InvalidName('Table name can not be empty');
            }
            self::$calculatedTableNames[static::class] =
                self::forceNamingScheme($tableName, static::getNamingSchemeTable());
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
     * @param string $var
     * @return string
     * @throws InvalidConfiguration
     */
    public static function getColumnName($var)
    {
        if (isset(static::$columnAliases[$var])) {
            return static::$columnAliases[$var];
        }

        if (!isset(self::$calculatedColumnNames[static::class][$var])) {
            static::$namingUsed = true;
            $colName = $var;

            if (static::$columnPrefix &&
                strpos(
                    $colName,
                    self::forceNamingScheme(static::$columnPrefix, static::getNamingSchemeColumn())
                ) !== 0) {
                $colName = static::$columnPrefix . $colName;
            }

            self::$calculatedColumnNames[static::class][$var] =
                self::forceNamingScheme($colName, static::getNamingSchemeColumn());
        }

        return self::$calculatedColumnNames[static::class][$var];
    }

    public static function getRelationDefinition($relation)
    {
        if (!isset(static::$relations[$relation])) {
            throw new UndefinedRelation('Relation ' . $relation . ' is not defined');
        }

        $relationDefinition = &static::$relations[$relation];

        if (isset($relationDefinition[0])) {
            // convert the short form
            $length = count($relationDefinition);

            if ($length === 2 && gettype($relationDefinition[1]) === 'array') {
                // owner of one-to-many or one-to-one
                static::$relations[$relation] = [
                    self::OPT_RELATION_CARDINALITY => 'one',
                    self::OPT_RELATION_CLASS       => $relationDefinition[0],
                    self::OPT_RELATION_REFERENCE   => $relationDefinition[1],
                ];
            } elseif ($length === 3 && $relationDefinition[0] === 'one') {
                // non-owner of one-to-one
                static::$relations[$relation] = [
                    self::OPT_RELATION_CARDINALITY => 'one',
                    self::OPT_RELATION_CLASS       => $relationDefinition[1],
                    self::OPT_RELATION_OPPONENT    => $relationDefinition[2],
                ];
            } elseif ($length === 2) {
                // non-owner of one-to-many
                static::$relations[$relation] = [
                    self::OPT_RELATION_CARDINALITY => 'many',
                    self::OPT_RELATION_CLASS       => $relationDefinition[0],
                    self::OPT_RELATION_OPPONENT    => $relationDefinition[1],
                ];
            } elseif ($length === 4 && gettype($relationDefinition[1]) === 'array') {
                static::$relations[$relation] = [
                    self::OPT_RELATION_CARDINALITY => 'many',
                    self::OPT_RELATION_CLASS       => $relationDefinition[0],
                    self::OPT_RELATION_REFERENCE   => $relationDefinition[1],
                    self::OPT_RELATION_OPPONENT    => $relationDefinition[2],
                    self::OPT_RELATION_TABLE       => $relationDefinition[3],
                ];
            } else {
                throw new InvalidConfiguration('Invalid short form for relation ' . $relation);
            }
        } elseif (empty($relationDefinition[self::OPT_RELATION_CARDINALITY])) {
            // default cardinality
            $relationDefinition[self::OPT_RELATION_CARDINALITY] =
                !empty($relationDefinition[self::OPT_RELATION_OPPONENT]) ? 'many' : 'one';
        } elseif (isset($relationDefinition[self::OPT_RELATION_REFERENCE]) &&
                  !isset($relationDefinition[self::OPT_RELATION_TABLE]) &&
                  $relationDefinition[self::OPT_RELATION_CARDINALITY] === 'many') {
            // overwrite wrong cardinality for owner
            $relationDefinition[self::OPT_RELATION_CARDINALITY] = 'one';
        }

        return $relationDefinition;
    }

    /**
     * @return string
     */
    public static function getTableNameTemplate()
    {
        return static::$tableNameTemplate;
    }

    /**
     * @param string $tableNameTemplate
     * @throws InvalidConfiguration
     */
    public static function setTableNameTemplate($tableNameTemplate)
    {
        if (static::$namingUsed) {
            throw new InvalidConfiguration('Template can not be changed afterwards');
        }

        static::$tableNameTemplate = $tableNameTemplate;
    }

    /**
     * @return string
     */
    public static function getNamingSchemeTable()
    {
        return static::$namingSchemeTable;
    }

    /**
     * @param string $namingSchemeTable
     * @throws InvalidConfiguration
     */
    public static function setNamingSchemeTable($namingSchemeTable)
    {
        if (static::$namingUsed) {
            throw new InvalidConfiguration('Naming scheme can not be changed afterwards');
        }

        static::$namingSchemeTable = $namingSchemeTable;
    }

    /**
     * @return string
     */
    public static function getNamingSchemeColumn()
    {
        return static::$namingSchemeColumn;
    }

    /**
     * @param string $namingSchemeColumn
     * @throws InvalidConfiguration
     */
    public static function setNamingSchemeColumn($namingSchemeColumn)
    {
        if (static::$namingUsed) {
            throw new InvalidConfiguration('Naming scheme can not be changed afterwards');
        }

        static::$namingSchemeColumn = $namingSchemeColumn;
    }

    /**
     * @return string
     */
    public static function getNamingSchemeMethods()
    {
        return static::$namingSchemeMethods;
    }

    /**
     * @param string $namingSchemeMethods
     * @throws InvalidConfiguration
     */
    public static function setNamingSchemeMethods($namingSchemeMethods)
    {
        if (static::$namingUsed) {
            throw new InvalidConfiguration('Naming scheme can not be changed afterwards');
        }

        static::$namingSchemeMethods = $namingSchemeMethods;
    }

    /**
     * Get the primary key vars
     *
     * The primary key can consist of multiple columns. You should configure the vars that are translated to these
     * columns.
     *
     * @return array
     */
    public static function getPrimaryKeyVars()
    {
        return !is_array(static::$primaryKey) ? [static::$primaryKey] : static::$primaryKey;
    }

    /**
     * Check if the table has a auto increment column.
     *
     * @return bool
     */
    public static function isAutoIncremented()
    {
        return count(static::getPrimaryKeyVars()) > 1 ? false : static::$autoIncrement;
    }

    /**
     * Enforce $namingScheme to $name
     *
     * Supported naming schemes: snake_case, snake_lower, SNAKE_UPPER, Snake_Ucfirst, camelCase, StudlyCaps, lower
     * and UPPER.
     *
     * @param string $name         The name of the var / column
     * @param string $namingScheme The naming scheme to use
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
     * Get reflection of the entity
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
     * Constructor
     *
     * It calls ::onInit() after initializing $data and $originalData.
     *
     * @param array         $data          The current data
     * @param EntityManager $entityManager The EntityManager that created this entity
     * @param bool          $fromDatabase  Whether or not the data comes from database
     */
    final public function __construct(array $data = [], EntityManager $entityManager = null, $fromDatabase = false)
    {
        if ($fromDatabase) {
            $this->originalData = $data;
        }
        $this->data = array_merge($this->data, $data);
        $this->entityManager = $entityManager;
        $this->onInit(!$fromDatabase);
    }

    /**
     * Set $var to $value
     *
     * Tries to call custom setter before it stores the data directly. If there is a setter the setter needs to store
     * data that should be updated in the database to $data. Do not store data in $originalData as it will not be
     * written and give wrong results for dirty checking.
     *
     * The onChange event is called after something got changed.
     *
     * @param string $var   The variable to change
     * @param mixed  $value The value to store
     * @throws InvalidConfiguration
     * @link https://tflori.github.io/orm/entities.html Working with entities
     */
    public function __set($var, $value)
    {
        $col = $this->getColumnName($var);

        static::$namingUsed = true;
        $setter = self::forceNamingScheme('set' . ucfirst($var), static::getNamingSchemeMethods());
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
     * Get the value from $var
     *
     * If there is a custom getter this method get called instead.
     *
     * @param string $var The variable to get
     * @return mixed|null
     * @throws InvalidConfiguration
     * @link https://tflori.github.io/orm/entities.html Working with entities
     */
    public function __get($var)
    {
        $getter = self::forceNamingScheme('get' . ucfirst($var), static::getNamingSchemeMethods());
        if (method_exists($this, $getter) && is_callable([$this, $getter])) {
            return $this->$getter();
        } else {
            $col = static::getColumnName($var);
            return isset($this->data[$col]) ? $this->data[$col] : null;
        }
    }

    /**
     * Checks if entity or $var got changed
     *
     * @param string $var Check only this variable or all variables
     * @return bool
     * @throws InvalidConfiguration
     */
    public function isDirty($var = null)
    {
        if ($var) {
            $col = static::getColumnName($var);
            return @$this->data[$col] !== @$this->originalData[$col];
        }

        ksort($this->data);
        ksort($this->originalData);

        return serialize($this->data) !== serialize($this->originalData);
    }

    /**
     * Resets the entity or $var to original data
     *
     * @param string $var Reset only this variable or all variables
     * @throws InvalidConfiguration
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
     * Save the entity to $entityManager
     *
     * @param EntityManager $entityManager
     * @return Entity
     * @throws Exceptions\NoConnection
     * @throws Exceptions\NoEntity
     * @throws Exceptions\NotScalar
     * @throws Exceptions\UnsupportedDriver
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws InvalidName
     * @throws NoEntityManager
     */
    public function save(EntityManager $entityManager = null)
    {
        $entityManager = $entityManager ?: $this->entityManager;

        if (!$entityManager) {
            throw new NoEntityManager('No entity manager given');
        }

        $inserted = false;
        $updated = false;

        try {
            // this may throw if the primary key is implemented but we using this to omit duplicated code
            if (!$entityManager->sync($this)) {
                $entityManager->insert($this, false);
                $inserted = true;
            } elseif ($this->isDirty()) {
                $this->preUpdate();
                $entityManager->update($this);
                $updated = true;
            }
        } catch (IncompletePrimaryKey $e) {
            if (static::isAutoIncremented()) {
                $this->prePersist();
                $id = $entityManager->insert($this);
                $this->data[static::getColumnName(static::getPrimaryKeyVars()[0])] = $id;
                $inserted = true;
            } else {
                throw $e;
            }
        }

        if ($inserted || $updated) {
            $inserted && $this->postPersist();
            $updated && $this->postUpdate();
            $entityManager->sync($this, true);
        }

        return $this;
    }

    public function fetch($relation, $entityManager = null)
    {
        $entityManager = $entityManager ?: $this->entityManager;

        if (!$entityManager) {
            throw new NoEntityManager('No entity manager given');
        }

        $relDef = static::getRelationDefinition($relation);
        $class = $relDef[self::OPT_RELATION_CLASS];

        if (isset($relDef[self::OPT_RELATION_REFERENCE]) &&
            !isset($relDef[self::OPT_RELATION_TABLE])) {
            $key = array_map([$this, '__get'], array_keys($relDef[self::OPT_RELATION_REFERENCE]));

            if (in_array(null, $key)) {
                return null;
            }

            return $entityManager->fetch($class, $key);
        }

        $fetcher = $entityManager->fetch($class);

        if ($relDef[self::OPT_RELATION_CARDINALITY] === 'one') {
            $fetcher->where('dmgd_id', 42);
            return $fetcher->one();
        }

        return $fetcher;
    }

    /**
     * Get the primary key
     *
     * @return array
     * @throws IncompletePrimaryKey
     */
    public function getPrimaryKey()
    {
        $primaryKey = [];
        foreach (static::getPrimaryKeyVars() as $var) {
            $value = $this->$var;
            if ($value === null) {
                throw new IncompletePrimaryKey('Incomplete primary key - missing ' . $var);
            }
            $primaryKey[$var] = $value;
        }
        return $primaryKey;
    }

    /**
     * Get current data
     *
     * @return array
     * @internal
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set new original data
     *
     * @param array $data
     * @internal
     */
    public function setOriginalData(array $data)
    {
        $this->originalData = $data;
    }

    /**
     * Empty event handler
     *
     * Get called when something is changed with magic setter.
     *
     * @param string $var The variable that got changed.merge(node.inheritedProperties)
     * @param mixed  $oldValue The old value of the variable
     * @param mixed  $value The new value of the variable
     */
    public function onChange($var, $oldValue, $value)
    {
    }

    /**
     * Empty event handler
     *
     * Get called when the entity get initialized.
     *
     * @param bool $new Whether or not the entity is new or from database
     */
    public function onInit($new)
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get inserted in database.
     */
    public function prePersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got inserted in database.
     */
    public function postPersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get updated in database.
     */
    public function preUpdate()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got updated in database.
     */
    public function postUpdate()
    {
    }

    /**
     * String representation of data
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of data
     */
    public function unserialize($serialized)
    {
        $this->data = unserialize($serialized);
        $this->onInit(false);
    }
}
