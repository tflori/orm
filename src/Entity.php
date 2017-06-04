<?php

namespace ORM;

use ORM\Dbal\Column;
use ORM\Exceptions\IncompletePrimaryKey;
use ORM\Exceptions\InvalidConfiguration;
use ORM\Exceptions\InvalidRelation;
use ORM\Exceptions\InvalidName;
use ORM\Exceptions\NoEntityManager;
use ORM\Exceptions\UndefinedRelation;
use ORM\Dbal\Error;
use ORM\Dbal\Table;
use ORM\EntityManager as EM;
use ORM\Exceptions\UnknownColumn;

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
    protected static $tableNameTemplate;

    /** The naming scheme to use for table names.
     * @var string */
    protected static $namingSchemeTable;

    /** The naming scheme to use for column names.
     * @var string */
    protected static $namingSchemeColumn;

    /** The naming scheme to use for method names.
     * @var string */
    protected static $namingSchemeMethods;

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

    /** Whether or not the validator for this class is enabled.
     * @var bool */
    protected static $enableValidator = false;

    /** Whether or not the validator for a class got enabled during runtime.
     * @var bool[] */
    protected static $enabledValidators = [];

    /** Relation definitions
     * @var array */
    protected static $relations = [];

    /** The reflections of the classes.
     * @internal
     * @var \ReflectionClass[] */
    protected static $reflections = [];

    /** The current data of a row.
     * @var mixed[] */
    protected $data = [];

    /** The original data of the row.
     * @var mixed[] */
    protected $originalData = [];

    /** The entity manager from which this entity got created
     * @var EM */
    protected $entityManager;

    /** Related objects for getRelated
     * @var array */
    protected $relatedObjects = [];

    /**
     * Constructor
     *
     * It calls ::onInit() after initializing $data and $originalData.
     *
     * @param mixed[] $data The current data
     * @param EM $entityManager The EntityManager that created this entity
     * @param bool $fromDatabase Whether or not the data comes from database
     */
    final public function __construct(array $data = [], EM $entityManager = null, $fromDatabase = false)
    {
        if ($fromDatabase) {
            $this->originalData = $data;
        }
        $this->data = array_merge($this->data, $data);
        $this->entityManager = $entityManager ?: EM::getInstance(static::class);
        $this->onInit(!$fromDatabase);
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
     * Get the column name of $attribute
     *
     * The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
     * to $namingSchemeColumn.
     *
     * **ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
     * the same as getColumnName($name).
     *
     * @param string $attribute
     * @return string
     * @throws InvalidConfiguration
     */
    public static function getColumnName($attribute)
    {
        if (isset(static::$columnAliases[$attribute])) {
            return static::$columnAliases[$attribute];
        }

        return EM::getInstance(static::class)->getNamer()
            ->getColumnName(static::class, $attribute, static::$columnPrefix, static::$namingSchemeColumn);
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
     * Get the definition for $relation
     *
     * It normalize the short definition form and create a Relation object from it.
     *
     * @param string $relation
     * @return Relation
     * @throws InvalidConfiguration
     * @throws UndefinedRelation
     */
    public static function getRelation($relation)
    {
        if (!isset(static::$relations[$relation])) {
            throw new UndefinedRelation('Relation ' . $relation . ' is not defined');
        }

        $relDef = &static::$relations[$relation];

        if (!$relDef instanceof Relation) {
            $relDef = Relation::createRelation($relation, $relDef);
        }

        return $relDef;
    }

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

        return EM::getInstance(static::class)->getNamer()
            ->getTableName(static::class, static::$tableNameTemplate, static::$namingSchemeTable);
    }

    /**
     * Check if the table has a auto increment column
     *
     * @return bool
     */
    public static function isAutoIncremented()
    {
        return count(static::getPrimaryKeyVars()) > 1 ? false : static::$autoIncrement;
    }

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
     * Validate $value for $attribute
     *
     * @param string $attribute
     * @param mixed $value
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
     * @param EM $entityManager
     * @return self
     */
    public function setEntityManager(EM $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get the value from $attribute
     *
     * If there is a custom getter this method get called instead.
     *
     * @param string $attribute The variable to get
     * @return mixed|null
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @link https://tflori.github.io/orm/entities.html Working with entities
     */
    public function __get($attribute)
    {
        $em = EM::getInstance(static::class);
        $getter = $em->getNamer()->getMethodName('get' . ucfirst($attribute), self::$namingSchemeMethods);

        if (method_exists($this, $getter) && is_callable([$this, $getter])) {
            return $this->$getter();
        } else {
            $col = static::getColumnName($attribute);
            $result = isset($this->data[$col]) ? $this->data[$col] : null;

            if (!$result && isset(static::$relations[$attribute]) && isset($this->entityManager)) {
                return $this->getRelated($attribute);
            }

            return $result;
        }
    }

    /**
     * Set $attribute to $value
     *
     * Tries to call custom setter before it stores the data directly. If there is a setter the setter needs to store
     * data that should be updated in the database to $data. Do not store data in $originalData as it will not be
     * written and give wrong results for dirty checking.
     *
     * The onChange event is called after something got changed.
     *
     * @param string $attribute The variable to change
     * @param mixed  $value The value to store
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @link https://tflori.github.io/orm/entities.html Working with entities
     */
    public function __set($attribute, $value)
    {
        $col = $this->getColumnName($attribute);

        $em = EM::getInstance(static::class);
        $setter = $em->getNamer()->getMethodName('set' . ucfirst($attribute), self::$namingSchemeMethods);

        if (method_exists($this, $setter) && is_callable([$this, $setter])) {
            $oldValue = $this->__get($attribute);
            $md5OldData = md5(serialize($this->data));
            $this->$setter($value);
            $changed = $md5OldData !== md5(serialize($this->data));
        } else {
            if (static::isValidatorEnabled()) {
                static::validate($attribute, $value);
            }

            $oldValue = $this->__get($attribute);
            $changed = (isset($this->data[$col]) ? $this->data[$col] : null) !== $value;
            $this->data[$col] = $value;
        }

        if ($changed) {
            $this->onChange($attribute, $oldValue, $this->__get($attribute));
        }
    }

    /**
     * Fill the entity with $data
     *
     * @param array $data
     * @param bool  $ignoreUnknown
     * @throws UnknownColumn
     */
    public function fill(array $data, $ignoreUnknown = false)
    {
        foreach ($data as $attribute => $value) {
            try {
                $this->__set($attribute, $value);
            } catch (UnknownColumn $e) {
                if (!$ignoreUnknown) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Get related objects
     *
     * The difference between getRelated and fetch is that getRelated stores the fetched entities. To refresh set
     * $refresh to true.
     *
     * @param string $relation
     * @param bool   $refresh
     * @return mixed
     * @throws Exceptions\NoConnection
     * @throws Exceptions\NoEntity
     * @throws IncompletePrimaryKey
     * @throws InvalidConfiguration
     * @throws NoEntityManager
     * @throws UndefinedRelation
     */
    public function getRelated($relation, $refresh = false)
    {
        if ($refresh || !isset($this->relatedObjects[$relation])) {
            $this->relatedObjects[$relation] = $this->fetch($relation, true);
        }

        return $this->relatedObjects[$relation];
    }

    /**
     * Set $relation to $entity
     *
     * This method is only for the owner of a relation.
     *
     * @param string $relation
     * @param Entity $entity
     * @throws IncompletePrimaryKey
     * @throws InvalidRelation
     */
    public function setRelated($relation, Entity $entity = null)
    {
        $this::getRelation($relation)->setRelated($this, $entity);

        $this->relatedObjects[$relation] = $entity;
    }

    /**
     * Add relations for $relation to $entities
     *
     * This method is only for many-to-many relations.
     *
     * This method does not take care about already existing relations and will fail hard.
     *
     * @param string        $relation
     * @param Entity[]      $entities
     * @throws NoEntityManager
     */
    public function addRelated($relation, array $entities)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 3 && func_get_arg(2) instanceof EM) {
            trigger_error(
                'Passing EntityManager to addRelated is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $this::getRelation($relation)->addRelated($this, $entities, $this->entityManager);
    }

    /**
     * Delete relations for $relation to $entities
     *
     * This method is only for many-to-many relations.
     *
     * @param string        $relation
     * @param Entity[]      $entities
     * @throws NoEntityManager
     */
    public function deleteRelated($relation, $entities)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 3 && func_get_arg(2) instanceof EM) {
            trigger_error(
                'Passing EntityManager to deleteRelated is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $this::getRelation($relation)->deleteRelated($this, $entities, $this->entityManager);
    }

    /**
     * Resets the entity or $attribute to original data
     *
     * @param string $attribute Reset only this variable or all variables
     * @throws InvalidConfiguration
     */
    public function reset($attribute = null)
    {
        if (!empty($attribute)) {
            $col = static::getColumnName($attribute);
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
     * Save the entity to EntityManager
     *
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
    public function save()
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 1 && func_get_arg(0) instanceof EM) {
            trigger_error(
                'Passing EntityManager to save is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $inserted = false;
        $updated = false;

        try {
            // this may throw if the primary key is auto incremented but we using this to omit duplicated code
            if (!$this->entityManager->sync($this)) {
                $this->entityManager->insert($this, false);
                $inserted = true;
            } elseif ($this->isDirty()) {
                $this->preUpdate();
                $this->entityManager->update($this);
                $updated = true;
            }
        } catch (IncompletePrimaryKey $e) {
            if (static::isAutoIncremented()) {
                $this->prePersist();
                $id = $this->entityManager->insert($this);
                $this->data[static::getColumnName(static::getPrimaryKeyVars()[0])] = $id;
                $inserted = true;
            } else {
                throw $e;
            }
        }

        if ($inserted || $updated) {
            $inserted && $this->postPersist();
            $updated && $this->postUpdate();
            $this->entityManager->sync($this, true);
        }

        return $this;
    }

    /**
     * Checks if entity or $attribute got changed
     *
     * @param string $attribute Check only this variable or all variables
     * @return bool
     * @throws InvalidConfiguration
     */
    public function isDirty($attribute = null)
    {
        if (!empty($attribute)) {
            $col = static::getColumnName($attribute);
            return (isset($this->data[$col]) ? $this->data[$col] : null) !==
                   (isset($this->originalData[$col]) ? $this->originalData[$col] : null);
        }

        ksort($this->data);
        ksort($this->originalData);

        return serialize($this->data) !== serialize($this->originalData);
    }

    /**
     * Empty event handler
     *
     * Get called when something is changed with magic setter.
     *
     * @param string $attribute The variable that got changed.merge(node.inheritedProperties)
     * @param mixed  $oldValue The old value of the variable
     * @param mixed  $value The new value of the variable
     */
    public function onChange($attribute, $oldValue, $value)
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
     * Get called before the entity get updated in database.
     */
    public function preUpdate()
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


    // DEPRECATED stuff

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
     * Get called after the entity got updated in database.
     */
    public function postUpdate()
    {
    }

    /**
     * Fetches related objects
     *
     * For relations with cardinality many it returns an EntityFetcher. Otherwise it returns the entity.
     *
     * It will throw an error for non owner when the key is incomplete.
     *
     * @param string        $relation      The relation to fetch
     * @param bool          $getAll
     * @return Entity|Entity[]|EntityFetcher
     * @throws NoEntityManager
     */
    public function fetch($relation, $getAll = false)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 3 && ($getAll instanceof EM || $getAll === null)) {
            $getAll = func_get_arg(2);
            trigger_error(
                'Passing EntityManager to fetch is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $relation = $this::getRelation($relation);

        if ($getAll) {
            return $relation->fetchAll($this, $this->entityManager);
        } else {
            return $relation->fetch($this, $this->entityManager);
        }
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
        foreach (static::getPrimaryKeyVars() as $attribute) {
            $value = $this->$attribute;
            if ($value === null) {
                throw new IncompletePrimaryKey('Incomplete primary key - missing ' . $attribute);
            }
            $primaryKey[$attribute] = $value;
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
     * String representation of data
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string
     */
    public function serialize()
    {
        return serialize([$this->data, $this->relatedObjects]);
    }

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of data
     */
    public function unserialize($serialized)
    {
        list($this->data, $this->relatedObjects) = unserialize($serialized);
        $this->entityManager = EM::getInstance(static::class);
        $this->onInit(false);
    }

    /**
     * @return string
     * @deprecated use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getTableNameTemplate()
    {
        return static::$tableNameTemplate;
    }

    /**
     * @param string $tableNameTemplate
     * @deprecated use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setTableNameTemplate($tableNameTemplate)
    {
        static::$tableNameTemplate = $tableNameTemplate;
    }

    /**
     * @return string
     * @deprecated use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeTable()
    {
        return static::$namingSchemeTable;
    }

    /**
     * @param string $namingSchemeTable
     * @deprecated use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeTable($namingSchemeTable)
    {
        static::$namingSchemeTable = $namingSchemeTable;
    }

    /**
     * @return string
     * @deprecated use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeColumn()
    {
        return static::$namingSchemeColumn;
    }

    /**
     * @param string $namingSchemeColumn
     * @deprecated use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeColumn($namingSchemeColumn)
    {
        static::$namingSchemeColumn = $namingSchemeColumn;
    }

    /**
     * @return string
     * @deprecated use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeMethods()
    {
        return static::$namingSchemeMethods;
    }

    /**
     * @param string $namingSchemeMethods
     * @deprecated use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeMethods($namingSchemeMethods)
    {
        static::$namingSchemeMethods = $namingSchemeMethods;
    }
}
