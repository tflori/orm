<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidRelation;
use ORM\Exception\InvalidType;
use ORM\Helper;
use ORM\Relation;

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
        if (is_array($morph)) {
            $this->morphMap = $morph;
        } else {
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

    public function setRelated(Entity $self, Entity $entity = null)
    {
        $type = $this->getType($entity);
        $self->setAttribute($this->morphColumn, $type);
        $reference = $this->getMorphedReference($type);

        // if the reference is different per type we clean other references
        if (is_array(Helper::first($this->reference)) && isset($this->reference[$type])) {
            $this->cleanOtherReferences($self, $type);
        }

        foreach ($reference as $fkAttribute => $attribute) {
            if ($entity === null) {
                $self->setAttribute($fkAttribute, null);
                continue;
            }

            $value = $entity->getAttribute($attribute);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $self->setAttribute($fkAttribute, $value);
        }
    }

    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        throw new InvalidRelation('Morphed relations do not allow joins');
    }

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

    protected function getMorphedReference($type)
    {
        if (!is_array(Helper::first($this->reference))) {
            return $this->reference;
        }

        if (isset($this->reference[$type])) {
            return $this->reference[$type];
        }

        $reference = [];
        foreach ($this->reference as $fkColumn => $pkMap) {
            if (!isset($pkMap[$type])) {
                throw new InvalidType(sprintf(
                    'Reference %s does not support type %s',
                    $this->name,
                    $type
                ));
            }
            $reference[$fkColumn] = $pkMap[$type];
        }
        return $reference;
    }

    protected function getType(Entity $entity)
    {
        if ($this->morphMap) {
            $class = get_class($entity);
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

        if (!$entity instanceof $this->super) {
            throw new InvalidType(sprintf(
                'Reference %s does not support entities of %s',
                $this->name,
                get_class($entity)
            ));
        }

        return get_class($entity); // what about mocks?
    }

    /**
     * @param Entity $self
     * @param string $newType
     */
    protected function cleanOtherReferences(Entity $self, $newType)
    {
        $otherReferences = array_filter($this->reference, function ($refType) use ($newType) {
            return $refType !== $newType;
        }, ARRAY_FILTER_USE_KEY);
        $referencedColumns = array_reduce($otherReferences, function ($carry, $reference) {
            return array_merge($carry, array_keys($reference));
        }, []);
        $referencedColumns = array_diff($referencedColumns, array_keys($this->reference[$newType]));
        foreach ($referencedColumns as $column) {
            $self->setAttribute($column, null);
        }
    }
}
