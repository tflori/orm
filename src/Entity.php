<?php

namespace ORM;

use ORM\Dbal\Error;
use ORM\Entity\GeneratesPrimaryKeys;
use ORM\Entity\Relations;
use ORM\Entity\Validation;
use ORM\EntityManager as EM;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\UnknownColumn;
use ReflectionClass;
use Serializable;

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
 * @link    https://tflori.github.io/orm/entityDefinition.html Entity Definition
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Entity implements Serializable
{
    use Validation, Relations;

    /** @deprecated Use Relation::OPT_CLASS instead */
    const OPT_RELATION_CLASS       = 'class';
    /** @deprecated Use Relation::OPT_CARDINALITY instead */
    const OPT_RELATION_CARDINALITY = 'cardinality';
    /** @deprecated Use Relation::OPT_REFERENCE instead */
    const OPT_RELATION_REFERENCE   = 'reference';
    /** @deprecated Use Relation::OPT_OPPONENT instead */
    const OPT_RELATION_OPPONENT    = 'opponent';
    /** @deprecated Use Relation::OPT_TABLE instead */
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

    /** The naming scheme to use for attributes.
     * @var string */
    protected static $namingSchemeAttributes;

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

    /** Additional attributes to show in toArray method
     * @var array  */
    protected static $includedAttributes = [];

    /** Attributes to hide for toArray method (overruled by $attributes parameter)
     * @var array  */
    protected static $excludedAttributes = [];

    /** The reflections of the classes.
     * @internal
     * @var ReflectionClass[] */
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

    /**
     * Constructor
     *
     * It calls ::onInit() after initializing $data and $originalData.
     *
     * @param mixed[] $data          The current data
     * @param EM      $entityManager The EntityManager that created this entity
     * @param bool    $fromDatabase  Whether or not the data comes from database
     */
    final public function __construct(array $data = [], EM $entityManager = null, $fromDatabase = false)
    {
        if ($fromDatabase) {
            $this->originalData = $data;
        }
        $this->data          = array_merge($this->data, $data);
        $this->entityManager = $entityManager ?: EM::getInstance(static::class);
        $this->onInit(!$fromDatabase);
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
     * Get the column name of $attribute
     *
     * The column names can not be specified by template. Instead they are constructed by $columnPrefix and enforced
     * to $namingSchemeColumn.
     *
     * **ATTENTION**: If your overwrite this method remember that getColumnName(getColumnName($name)) have to be exactly
     * the same as getColumnName($name).
     *
     * @param string $column
     * @return string
     */
    public static function getAttributeName($column)
    {
        $attributeName = array_search($column, static::$columnAliases);
        if ($attributeName !== false) {
            return $attributeName;
        }

        return EM::getInstance(static::class)->getNamer()
            ->getAttributeName($column, static::$columnPrefix, static::$namingSchemeAttributes);
    }

    /**
     * Create an entityFetcher for this entity
     *
     * @return EntityFetcher
     */
    public static function query()
    {
        return EM::getInstance(static::class)->fetch(static::class);
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
        return !is_array(static::$primaryKey) ? [ static::$primaryKey ] : static::$primaryKey;
    }

    /**
     * Get the table name
     *
     * The table name is constructed by $tableNameTemplate and $namingSchemeTable. It can be overwritten by
     * $tableName.
     *
     * @return string
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
     * @param EM $entityManager
     * @return static
     */
    public function setEntityManager(EM $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @param string $attribute
     * @return mixed|null
     * @see self::getAttribute
     * @codeCoverageIgnore Alias for getAttribute
     */
    public function __get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    /**
     * Get the value from $attribute
     *
     * If there is a custom getter this method get called instead.
     *
     * @param string $attribute The variable to get
     * @return mixed|null
     * @link https://tflori.github.io/orm/entities.html Working with entities
     */
    public function getAttribute($attribute)
    {
        $em     = EM::getInstance(static::class);
        $getter = $em->getNamer()->getMethodName('get' . ucfirst($attribute), self::$namingSchemeMethods);

        if (method_exists($this, $getter) && is_callable([ $this, $getter ])) {
            return $this->$getter();
        } else {
            $col    = static::getColumnName($attribute);
            $result = isset($this->data[$col]) ? $this->data[$col] : null;

            if (!$result && isset(static::$relations[$attribute]) && isset($this->entityManager)) {
                return $this->getRelated($attribute);
            }

            return $result;
        }
    }

    /**
     * Check if a column is defined
     *
     * @param $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        $em     = EM::getInstance(static::class);
        $getter = $em->getNamer()->getMethodName('get' . ucfirst($attribute), self::$namingSchemeMethods);

        if (method_exists($this, $getter) && is_callable([ $this, $getter ])) {
            return $this->$getter() !== null;
        } else {
            $col = static::getColumnName($attribute);
            $isset = isset($this->data[$col]);

            if (!$isset && isset(static::$relations[$attribute])) {
                return !empty($this->getRelated($attribute));
            }

            return $isset;
        }
    }

    /**
     * @param string $attribute The variable to change
     * @param mixed $value The value to store
     * @see self::getAttribute
     * @codeCoverageIgnore Alias for getAttribute
     */
    public function __set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);
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
     * The method throws an error when the validation fails (also when the column does not exist).
     *
     * @param string $attribute The variable to change
     * @param mixed $value The value to store
     * @return static
     * @link https://tflori.github.io/orm/entities.html Working with entities
     * @throws Error
     */
    public function setAttribute($attribute, $value)
    {
        $col = $this->getColumnName($attribute);

        $em     = EM::getInstance(static::class);
        $setter = $em->getNamer()->getMethodName('set' . ucfirst($attribute), self::$namingSchemeMethods);

        if (method_exists($this, $setter) && is_callable([ $this, $setter ])) {
            $oldValue   = $this->__get($attribute);
            $md5OldData = md5(serialize($this->data));
            $this->$setter($value);
            $changed = $md5OldData !== md5(serialize($this->data));
        } else {
            if (static::isValidatorEnabled()) {
                $error = static::validate($attribute, $value);
                if ($error instanceof Error) {
                    throw $error;
                }
            }

            $oldValue         = $this->__get($attribute);
            $changed          = (isset($this->data[$col]) ? $this->data[$col] : null) !== $value;
            $this->data[$col] = $value;
        }

        if ($changed) {
            $this->onChange($attribute, $oldValue, $this->__get($attribute));
        }

        return $this;
    }

    /**
     * Fill the entity with $data
     *
     * When $checkMissing is set to true it also proves that the absent columns are nullable.
     *
     * @param array $data
     * @param bool $ignoreUnknown
     * @param bool $checkMissing
     * @throws UnknownColumn
     * @throws Error
     */
    public function fill(array $data, $ignoreUnknown = false, $checkMissing = false)
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

        if ($checkMissing && is_array($errors = $this->isValid())) {
            throw $errors[0];
        }
    }

    /**
     * Resets the entity or $attribute to original data
     *
     * @param string $attribute Reset only this variable or all variables
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
     * @throws IncompletePrimaryKey
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
        $updated  = false;

        try {
            // this may throw if the primary key is auto incremented but we using this to omit duplicated code
            if (!$this->entityManager->sync($this)) {
                $this->prePersist();
                $inserted = $this->entityManager->insert($this, false);
            } elseif ($this->isDirty()) {
                $this->preUpdate();
                $updated = $this->entityManager->update($this);
            }
        } catch (IncompletePrimaryKey $e) {
            if (static::isAutoIncremented()) {
                $this->prePersist();
                $inserted = $this->entityManager->insert($this);
            } elseif ($this instanceof GeneratesPrimaryKeys) {
                $this->generatePrimaryKey();
                $this->prePersist();
                $inserted = $this->entityManager->insert($this);
            } else {
                throw $e;
            }
        }

        $inserted && $this->postPersist();
        $updated && $this->postUpdate();

        return $this;
    }

    /**
     * Generates a primary key
     *
     * This method should only be executed from save method.
     * @codeCoverageIgnore no operations
     */
    protected function generatePrimaryKey()
    {
        // no operation by default
    }

    /**
     * Checks if entity or $attribute got changed
     *
     * @param string $attribute Check only this variable or all variables
     * @return bool
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
     * Get called when the entity get initialized.
     *
     * @param bool $new Whether or not the entity is new or from database
     * @codeCoverageIgnore dummy event handler
     */
    public function onInit($new)
    {
    }

    /**
     * Empty event handler
     *
     * Get called when something is changed with magic setter.
     *
     * @param string $attribute The variable that got changed.merge(node.inheritedProperties)
     * @param mixed  $oldValue  The old value of the variable
     * @param mixed  $value     The new value of the variable
     * @codeCoverageIgnore dummy event handler
     */
    public function onChange($attribute, $oldValue, $value)
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get inserted in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function prePersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get updated in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function preUpdate()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got inserted in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function postPersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got updated in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function postUpdate()
    {
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
     * Get an array of the entity
     *
     * @param array $attributes
     * @param bool $includeRelations
     * @return array
     */
    public function toArray(array $attributes = [], $includeRelations = true)
    {
        if (empty($attributes)) {
            $attributes = array_keys(static::$columnAliases);
            $attributes = array_merge($attributes, array_map([$this, 'getAttributeName'], array_keys($this->data)));
            $attributes = array_merge($attributes, static::$includedAttributes);
            $attributes = array_diff($attributes, static::$excludedAttributes);
        }

        $values = array_map(function ($attribute) {
            return $this->getAttribute($attribute);
        }, $attributes);

        $result = (array)array_combine($attributes, $values);

        if ($includeRelations) {
            foreach ($this->relatedObjects as $relation => $relatedObject) {
                if (is_array($relatedObject)) {
                    $result[$relation] = array_map(function (Entity $relatedObject) {
                        return $relatedObject->toArray();
                    }, $relatedObject);
                } elseif ($relatedObject instanceof Entity) {
                    $result[$relation] = $relatedObject->toArray();
                }
            }
        }

        return $result;
    }

    /**
     * String representation of data
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string
     */
    public function serialize()
    {
        return serialize([ $this->data, $this->relatedObjects ]);
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

    // DEPRECATED stuff

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getTableNameTemplate()
    {
        return static::$tableNameTemplate;
    }

    /**
     * @param string $tableNameTemplate
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setTableNameTemplate($tableNameTemplate)
    {
        static::$tableNameTemplate = $tableNameTemplate;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeTable()
    {
        return static::$namingSchemeTable;
    }

    /**
     * @param string $namingSchemeTable
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeTable($namingSchemeTable)
    {
        static::$namingSchemeTable = $namingSchemeTable;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeColumn()
    {
        return static::$namingSchemeColumn;
    }

    /**
     * @param string $namingSchemeColumn
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeColumn($namingSchemeColumn)
    {
        static::$namingSchemeColumn = $namingSchemeColumn;
    }

    /**
     * @return string
     * @deprecated         use getOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function getNamingSchemeMethods()
    {
        return static::$namingSchemeMethods;
    }

    /**
     * @param string $namingSchemeMethods
     * @deprecated         use setOption from EntityManager
     * @codeCoverageIgnore deprecated
     */
    public static function setNamingSchemeMethods($namingSchemeMethods)
    {
        static::$namingSchemeMethods = $namingSchemeMethods;
    }
}
