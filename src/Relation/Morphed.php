<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidRelation;
use ORM\Exception\InvalidType;
use ORM\Helper;
use ORM\QueryBuilder\Parenthesis;

class Morphed extends Owner
{
    /** Reference definition
     * @var array */
    protected $reference;

    /** Column where the type gets persisted
     * @var string */
    protected $morphColumn;

    /** Array of value => class pairs
     * @var array */
    protected $morphMap;

    /** The parent class that all morphed elements have to extend
     * @var string */
    protected $super;

    /**
     * Morphed constructor.
     * @param string $morphColumn
     * @param string|array $morph
     * @param array $reference
     */
    public function __construct($morphColumn, $morph, array $reference)
    {
        $this->morphColumn = $morphColumn;
        $this->reference = $reference;
        $referenceKeys = array_keys($reference);
        $firstReference = Helper::first($reference);
        if (is_array($morph)) {
            $this->morphMap = $morph;
        } else {
            if (class_exists($referenceKeys[0])) {
                $this->morphMap = array_combine($referenceKeys, $referenceKeys);
            } elseif (is_array($firstReference)) {
                $classes = array_keys($firstReference);
                $this->morphMap = array_combine($classes, $classes);
            }
            $this->super = $morph;
        }
    }

    /** {@inheritDoc} */
    public static function fromShort(array $short)
    {
        if ($short[0] === self::CARDINALITY_ONE) {
            array_shift($short);
        }

        if (count($short) === 2 && is_array($short[0]) && count($short[0]) === 1 && is_array($short[1])) {
            foreach ($short[0] as $morphColumn => $morph) {
                return new self($morphColumn, $morph, $short[1]);
            }
        }
        return null;
    }

    /** {@inheritDoc} */
    public function apply(EntityFetcher $fetcher, Entity $entity)
    {
        $type = $this->getType($entity);
        $fetcher->where($this->morphColumn, $type);

        $reference = $this->getMorphedReference($type);
        $foreignKey = $this->getForeignKey($entity, array_flip($reference));
        foreach ($foreignKey as $col => $value) {
            $fetcher->where($col, $value);
        }
    }

    /** {@inheritDoc} */
    public function applyJoin(Parenthesis $join, $yourAlias, OneToMany $opponent)
    {
        $type = $this->getType($opponent->parent);
        $join->where(sprintf('%s.%s', $opponent->name, $this->morphColumn), '=', $type);

        $reference = $this->getMorphedReference($type);
        foreach ($reference as $myColumn => $yourColumn) {
            $join->where(sprintf("%s.%s = %s.%s", $yourAlias, $yourColumn, $opponent->name, $myColumn));
        }
    }

    /** {@inheritDoc} */
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        $type = $self->getAttribute($this->morphColumn);
        $class = $this->getMorphedClass($type);
        $reference = $this->getMorphedReference($type);
        $key = array_map([$self, 'getAttribute' ], array_keys($reference));

        if (in_array(null, $key)) {
            return null;
        }

        return $entityManager->fetch($class, $key);
    }

    /** {@inheritDoc} */
    public function setRelated(Entity $self, Entity $entity = null)
    {
        if ($entity === null) {
            $self->setAttribute($this->morphColumn, null);
            $this->cleanReferences($self);
            return;
        }

        $type = $this->getType($entity);
        $self->setAttribute($this->morphColumn, $type);
        $reference = $this->getMorphedReference($type);

        // if the reference is different per type we clean other references
        $this->cleanReferences($self, $type);

        foreach ($reference as $fkAttribute => $attribute) {
            $value = $entity->getAttribute($attribute);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $self->setAttribute($fkAttribute, $value);
        }
    }

    /**
     * Get the class for $type
     *
     * @param string $type
     * @return string
     * @throws InvalidType
     */
    protected function getMorphedClass($type)
    {
        if ($this->morphMap) {
            if (!isset($this->morphMap[$type])) {
                throw new InvalidType(sprintf(
                    'Reference %s does not support type %s',
                    $this->name,
                    $type
                ));
            }
            return $this->morphMap[$type];
        }

        return $type;
    }

    /**
     * Get the reference for $type
     *
     * @param string $type
     * @return array
     */
    protected function getMorphedReference($type)
    {
        // the reference defines only a foreign key
        if (count($this->reference) === 1 && isset($this->reference[0])) {
            // then the primary key is referenced
            $class = class_exists($type) ? $type : $this->getMorphedClass($type);
            /** @var Entity|string $class */
            return [$this->reference[0] => $class::getPrimaryKeyVars()[0]];
        }

        if (!is_array(Helper::first($this->reference))) {
            return $this->reference;
        }

        if ($this->morphMap && isset($this->reference[$type])) {
            return $this->reference[$type];
        }

        return array_map(function ($pkMap) use ($type) {
            return $pkMap[$type];
        }, $this->reference);
    }

    /**
     * Get the type of an entity
     *
     * Because of morph maps the type could be something different than the class name.
     *
     * If the type is not a subclass of $this->super or the it throws.
     *
     * @param Entity|string $class
     * @return int|string
     * @throws InvalidType
     */
    protected function getType($class)
    {
        if ($class instanceof Entity) {
            $entity = $class;
            $class = get_class($class);
        }

        if ($this->morphMap) {
            $type = array_search($class, $this->morphMap);
            if (!$type) {
                // maybe try with instance of then?
                throw new InvalidType(sprintf(
                    'Reference %s does not support entities of %s',
                    $this->name,
                    $class
                ));
            }
            return $type;
        }

        if (isset($entity) && !$entity instanceof $this->super) {
            throw new InvalidType(sprintf(
                'Reference %s does not support entities of %s',
                $this->name,
                $class
            ));
        }

        return $class; // what about mocks?
    }

    /**
     * Belongs to set related
     *
     * When we define different columns per type for the id we have to clean up every column
     * that is currently unused to prevent cascaded removals.
     *
     * @param Entity $self
     * @param string $newType
     */
    protected function cleanReferences(Entity $self, $newType = null)
    {
        $fkByType = $this->hasForeignKeysByType();
        if (!$fkByType && $newType) {
            return;
        }

        $fkAttributes = array_keys($this->reference);
        if ($fkByType) {
            $fkAttributes = array_reduce($this->reference, function ($carry, $reference) {
                return array_merge($carry, array_keys($reference));
            }, []);

            if ($newType !== null) {
                $fkAttributes = array_diff($fkAttributes, array_keys($this->reference[$newType]));
            }
        }

        // the reference defines only a foreign key
        if (count($this->reference) === 1 && isset($this->reference[0])) {
            $self->setAttribute($this->reference[0], null);
        }

        foreach ($fkAttributes as $column) {
            $self->setAttribute($column, null);
        }
    }

    /**
     * This method is not available for morphed relations.
     *
     * @param EntityFetcher $fetcher
     * @param string $join
     * @param string $alias
     * @internal
     * @throws InvalidRelation
     */
    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        throw new InvalidRelation('Morphed relations do not allow joins');
    }

    /**
     * Check if the foreign keys differ by type
     *
     * @return bool
     */
    protected function hasForeignKeysByType()
    {
        return $this->morphMap && is_array(Helper::first($this->reference)) &&
            array_diff(array_keys($this->morphMap), array_keys($this->reference)) === [];
    }
}
