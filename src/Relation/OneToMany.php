<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\InvalidConfiguration;
use ORM\Relation;

/**
 * OneToMany Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class OneToMany extends Relation
{
    /**
     * Owner constructor.
     *
     * @param string $name
     * @param string $class
     * @param string $opponent
     */
    public function __construct($name, $class, $opponent)
    {
        $this->name     = $name;
        $this->class    = $class;
        $this->opponent = $opponent;
    }

    /** {@inheritdoc} */
    public function fetch(Entity $me, EntityManager $entityManager)
    {
        $reference = $this->getOpponent()->getReference();
        if (empty($reference)) {
            throw new InvalidConfiguration('Reference is not defined in opponent');
        }
        $foreignKey = $this->getForeignKey($me, array_flip($reference));

        /** @var EntityFetcher $fetcher */
        $fetcher = $entityManager->fetch($this->class);
        foreach ($foreignKey as $col => $value) {
            $fetcher->where($col, $value);
        }

        return $fetcher;
    }

    /** {@inheritdoc} */
    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        $expression = [];
        foreach ($this->getOpponent()->getReference() as $hisVar => $myVar) {
            $expression[] = $alias . '.' . $myVar . ' = ' . $this->name . '.' . $hisVar;
        }

        call_user_func([ $fetcher, $join ], $this->class, implode(' AND ', $expression), $this->name, [], true);
    }
}
