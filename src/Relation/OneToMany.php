<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;
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
     * @param FilterInterface[] $filters
     */
    public function __construct($name, $class, $opponent, array $filters = [])
    {
        $this->name = $name;
        $this->class = $class;
        $this->opponent = $opponent;
        $this->filters = $filters;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfiguration
     */
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        $owner = $this->getOpponent();
        if (!$owner instanceof Owner) {
            throw new InvalidConfiguration(sprintf(
                'No owner defined for relation %s:%s referencing %s:%s',
                get_class($self),
                $this->name,
                $this->class,
                $this->opponent
            ));
        }

        /** @var EntityFetcher $fetcher */
        $fetcher = $entityManager->fetch($this->class);
        $owner->apply($fetcher, $self);

        foreach ($this->filters as $filter) {
            $fetcher->filter($filter);
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
