<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidRelation;
use ORM\Relation;

/**
 * Owner Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class Owner extends Relation
{
    /** Reference definition as key value pairs
     * @var array */
    protected $reference;

    /**
     * Owner constructor.
     *
     * @param string $name
     * @param string $class
     * @param array  $reference
     */
    public function __construct($name, $class, array $reference)
    {
        $this->name      = $name;
        $this->class     = $class;
        $this->reference = $reference;
    }

    /** {@inheritdoc} */
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        $key = array_map([$self, 'getAttribute' ], array_keys($this->reference));

        if (in_array(null, $key)) {
            return null;
        }

        return $entityManager->fetch($this->class, $key);
    }

    public function apply(EntityFetcher $fetcher, Entity $entity)
    {
        $foreignKey = $this->getForeignKey($entity, array_flip($this->reference));
        foreach ($foreignKey as $col => $value) {
            $fetcher->where($col, $value);
        }
    }

    /**
     * {@inheritdoc}
     * @throws InvalidRelation
     * @throws IncompletePrimaryKey
     */
    public function setRelated(Entity $self, Entity $entity = null)
    {
        if ($entity !== null && !$entity instanceof $this->class) {
            throw new InvalidRelation('Invalid entity for relation ' . $this->name);
        }

        foreach ($this->reference as $fkAttribute => $attribute) {
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

    /** {@inheritdoc} */
    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        $expression = [];
        foreach ($this->reference as $myVar => $hisVar) {
            $expression[] = $alias . '.' . $myVar . ' = ' . $this->name . '.' . $hisVar;
        }

        call_user_func([ $fetcher, $join ], $this->class, implode(' AND ', $expression), $this->name, [], true);
    }
}
