<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidRelation;
use ORM\Relation;

/**
 * ManyToMany Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class ManyToMany extends Relation
{
    /** The table that holds the foreign keys
     * @var string */
    protected $table;

    /**
     * ManyToMany constructor.
     *
     * @param string $name
     * @param string $class
     * @param array $reference
     * @param string $opponent
     * @param string $table
     * @param FilterInterface[] $filters
     */
    public function __construct($name, $class, array $reference, $opponent, $table, array $filters = [])
    {
        $this->name = $name;
        $this->class = $class;
        $this->opponent = $opponent;
        $this->reference = $reference;
        $this->table = $table;
        $this->filters = $filters;
    }

    public static function fromShort($name, array $short)
    {
        // remove cardinality if given
        if ($short[0] === self::CARDINALITY_MANY) {
            array_shift($short);
        }

        // get filters
        $filters = [];
        if (count($short) === 5 && is_array($short[4])) {
            $filters = array_map([self::class, 'createFilter'], array_pop($short));
        }

        if (count($short) === 4 &&
            is_string($short[0]) && is_array($short[1]) && is_string($short[2]) && is_string($short[3])
        ) {
            return new self($name, $short[0], $short[1], $short[2], $short[3], $filters);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /** {@inheritdoc} */
    public function fetch(Entity $self, EntityManager $entityManager)
    {

        $foreignKey = $this->getForeignKey($self, $this->reference);
        /** @var EntityFetcher $fetcher */
        $fetcher = $entityManager->fetch($this->class);
        $table   = $entityManager->escapeIdentifier($this->table);

        $expression = [];
        foreach ($this->getOpponent()->getReference() as $t0Var => $fkCol) {
            $expression[] = $table . '.' . $entityManager->escapeIdentifier($fkCol) . ' = t0.' . $t0Var;
        }

        $fetcher->join($table, implode(' AND ', $expression));

        foreach ($foreignKey as $col => $value) {
            $fetcher->where($table . '.' . $entityManager->escapeIdentifier($col), $value);
        }

        foreach ($this->filters as $filter) {
            $fetcher->filter($filter);
        }
        return $fetcher;
    }

    /** {@inheritdoc}
     * @throws IncompletePrimaryKey
     * @throws InvalidRelation
     * @throws IncompletePrimaryKey
     */
    public function addRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        if (empty($entities)) {
            return;
        }

        $table = $entityManager->escapeIdentifier($this->table);

        $cols            = [];
        $baseAssociation = [];
        foreach ($this->reference as $myVar => $fkCol) {
            $cols[] = $entityManager->escapeIdentifier($fkCol);
            $value  = $self->__get($myVar);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $baseAssociation[] = $entityManager->escapeValue($value);
        }

        $associations = [];
        foreach ($entities as $entity) {
            if (!$entity instanceof $this->class) {
                throw new InvalidRelation('Invalid entity for relation ' . $this->name);
            }

            $association = $baseAssociation;
            foreach ($this->getOpponent()->getReference() as $hisVar => $fkCol) {
                if (empty($associations)) {
                    $cols[] = $entityManager->escapeIdentifier($fkCol);
                }
                $value = $entity->__get($hisVar);

                if ($value === null) {
                    throw new IncompletePrimaryKey('Key incomplete to save foreign key');
                }

                $association[] = $entityManager->escapeValue($value);
            }
            $associations[] = implode(',', $association);
        }

        $statement = 'INSERT INTO ' . $table . ' (' . implode(',', $cols) . ') ' .
                     'VALUES (' . implode('),(', $associations) . ')';
        $entityManager->getConnection()->query($statement);
    }

    /** {@inheritdoc}
     * @throws IncompletePrimaryKey
     * @throws InvalidRelation
     * @throws IncompletePrimaryKey
     */
    public function deleteRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        if (empty($entities)) {
            return;
        }

        $table = $entityManager->escapeIdentifier($this->table);
        $where = [];

        foreach ($this->reference as $myVar => $fkCol) {
            $value = $self->__get($myVar);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $where[] = $entityManager->escapeIdentifier($fkCol) . ' = ' .
                       $entityManager->escapeValue($value);
        }

        foreach ($entities as $entity) {
            if (!$entity instanceof $this->class) {
                throw new InvalidRelation('Invalid entity for relation ' . $this->name);
            }

            $condition = [];
            foreach ($this->getOpponent()->getReference() as $hisVar => $fkCol) {
                $value = $entity->__get($hisVar);

                if ($value === null) {
                    throw new IncompletePrimaryKey('Key incomplete to save foreign key');
                }

                $condition[] = $entityManager->escapeIdentifier($fkCol) . ' = ' .
                               $entityManager->escapeValue($value);
            }
            $where[] = implode(' AND ', $condition);
        }

        $statement = 'DELETE FROM ' . $table . ' WHERE ' . array_shift($where) . ' ' .
                     'AND (' . implode(' OR ', $where) . ')';
        $entityManager->getConnection()->query($statement);
    }

    /** {@inheritdoc} */
    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        $table = $fetcher->getEntityManager()->escapeIdentifier($this->table);

        $expression = [];
        foreach ($this->reference as $myVar => $col) {
            $expression[] = $alias . '.' . $myVar . ' = ' .
                            $table . '.' . $fetcher->getEntityManager()->escapeIdentifier($col);
        }

        call_user_func([ $fetcher, $join ], $table, implode(' AND ', $expression), null, [], true);

        $expression = [];
        foreach ($this->getOpponent()->getReference() as $hisVar => $col) {
            $expression[] = $table . '.' . $fetcher->getEntityManager()->escapeIdentifier($col) .
                            ' = ' . $this->name . '.' . $hisVar;
        }

        call_user_func([ $fetcher, $join ], $this->class, implode(' AND ', $expression), $this->name, [], true);
    }
}
