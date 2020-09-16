<?php

namespace ORM;

use ORM\Dbal\Error;
use ORM\Entity\EventHandlers;
use ORM\Entity\GeneratesPrimaryKeys;
use ORM\Entity\Naming;
use ORM\Entity\Relations;
use ORM\Entity\Validation;
use ORM\EntityManager as EM;
use ORM\Event\Changed;
use ORM\Event\Fetched;
use ORM\Event\Inserted;
use ORM\Event\Inserting;
use ORM\Event\Saved;
use ORM\Event\Saving;
use ORM\Event\Updated;
use ORM\Event\Updating;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\UnknownColumn;
use ORM\Observer\AbstractObserver;
use ORM\Observer\CallbackObserver;
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
    use Validation, Relations, Naming, EventHandlers;

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

    /** The variable(s) used for primary key.
     * @var string[]|string */
    protected static $primaryKey = ['id'];

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
        $this->data          = array_merge($this->data, $data);
        $this->entityManager = $entityManager ?: EM::getInstance(static::class);
        if ($fromDatabase) {
            $this->originalData = $data;
            $this->entityManager->fire(new Fetched($this, $data));
        }
        $this->onInit(!$fromDatabase);
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
     * Observe the class using $observer
     *
     * If AbstractObserver is omitted it returns a new CallbackObserver. Usage example:
     * ```php
     * $em->observe(User::class)
     *     ->on('inserted', function (User $user) { ... })
     *     ->on('deleted', function (User $user) { ... });
     * ```
     *
     * For more information about model events please consult the [documentation](https://tflori.github.io/
     *
     * @param ?AbstractObserver $observer
     * @return ?CallbackObserver
     * @codeCoverageIgnore proxy for EntityManager::observe()
     *@see EntityManager::observe()
     */
    public static function observeBy(AbstractObserver $observer = null)
    {
        return EM::getInstance(static::class)->observe(static::class, $observer);
    }

    /**
     * Stop observing the class by $observer
     *
     * @param AbstractObserver $observer
     * @codeCoverageIgnore proxy for EntityManager::ignore()
     *@see EntityManager::detach()
     */
    public static function detachObserver(AbstractObserver $observer)
    {
        EM::getInstance(static::class)->detach($observer, static::class);
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
            $oldValue   = $this->getAttribute($attribute);
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

            $oldValue         = $this->getAttribute($attribute);
            $changed          = (isset($this->data[$col]) ? $this->data[$col] : null) !== $value;
            $this->data[$col] = $value;
        }

        if ($changed) {
            $newValue = $this->__get($attribute);
            $this->entityManager->fire(new Changed($this, $attribute, $oldValue, $newValue));
            $this->onChange($attribute, $oldValue, $newValue);
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
        if ($this->entityManager->fire(new Saving($this)) !== false) {
            $hasPrimaryKey = $this->hasPrimaryKey();
            if (!$hasPrimaryKey || !$this->entityManager->sync($this)) {
                $saved = $this->insertEntity($hasPrimaryKey);
            } else {
                $dirty = $this->getDirty();
                $saved = $this->updateEntity($dirty);
            }

            if ($saved) {
                $this->entityManager->fire(new Saved($saved));
            }
        }

        return $this;
    }

    /**
     * Insert the row in the database
     *
     * @param bool $hasPrimaryKey
     * @return Inserted|null
     * @throws IncompletePrimaryKey
     */
    private function insertEntity($hasPrimaryKey)
    {
        if (!$hasPrimaryKey && !static::isAutoIncremented()) {
            if (!$this instanceof GeneratesPrimaryKeys) {
                $this->getPrimaryKey(); // this will throw then...
            }
            $this->generatePrimaryKey();
        }

        if ($this->entityManager->fire(new Inserting($this)) !== false) {
            $this->prePersist();
            if ($this->entityManager->insert($this, !$hasPrimaryKey)) {
                $this->entityManager->fire($event = new Inserted($this));
                $this->postPersist();
                return $event;
            }
        }

        return null;
    }

    /**
     * Update the row in the database
     *
     * @param array $dirty
     * @return Updated|null
     */
    private function updateEntity(array $dirty)
    {
        if ($this->isDirty() && $this->entityManager->fire(new Updating($this, $dirty)) !== false) {
            $this->preUpdate();
            if ($this->entityManager->update($this)) {
                $this->entityManager->fire($event = new Updated($this, $dirty));
                $this->postUpdate();
                return $event;
            }
        }

        return null;
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
            $current = $this->getAttribute($attribute);
            $backupData = $this->data;
            $this->data = $this->originalData;
            $old = $this->getAttribute($attribute);
            $this->data = $backupData;
            return $current !== $old;
        }

        ksort($this->data);
        ksort($this->originalData);

        return serialize($this->data) !== serialize($this->originalData);
    }

    /**
     * Get an array of attributes that changed
     *
     * This method works on application level. Meaning it is showing additional attributes defined in
     * ::$includedAttributes and and hiding ::$excludedAttributes.
     *
     * @return array
     */
    public function getDirty()
    {
        $current = $this->toArray([], false);
        if (empty($this->originalData)) {
            return array_map(function ($value) {
                return [null, $value];
            }, $current);
        }

        $dirty = [];
        $backupData = $this->data;
        $this->data = $this->originalData;
        $old = $this->toArray([], false);
        $this->data = $backupData;
        foreach (array_merge(array_keys($current), array_keys($old)) as $key) {
            if (@$current[$key] === @$old[$key]) {
                continue;
            }
            $dirty[$key] = [@$old[$key], @$current[$key]];
        }

        return $dirty;
    }

    /**
     * Check if the entity has has a complete primary key
     *
     * @return bool
     */
    public function hasPrimaryKey()
    {
        return (bool)min(array_map([$this, '__isset'], static::getPrimaryKeyVars()));
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
}
