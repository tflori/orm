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
    public function fetch(Entity $me, EntityManager $entityManager)
    {
        $key = array_map([ $me, '__get' ], array_keys($this->reference));

        if (in_array(null, $key)) {
            return null;
        }

        return $entityManager->fetch($this->class, $key);
    }

    /** {@inheritdoc} */
    public function setRelated(Entity $me, Entity $entity = null)
    {
        if ($entity !== null && !$entity instanceof $this->class) {
            throw new InvalidRelation('Invalid entity for relation ' . $this->name);
        }

        foreach ($this->reference as $fkAttribute => $attribute) {
            if ($entity === null) {
                $me->__set($fkAttribute, null);
                continue;
            }

            $value = $entity->__get($attribute);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $me->__set($fkAttribute, $value);
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
