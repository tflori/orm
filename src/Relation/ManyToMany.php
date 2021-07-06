<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidRelation;
use ORM\Helper;
use ORM\Relation;

/**
 * ManyToMany Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class ManyToMany extends Relation
{
    use HasOpponent;

    /** The table that holds the foreign keys
     * @var string */
    protected $table;

    /**
     * ManyToMany constructor.
     *
     * @param string $class
     * @param array $reference
     * @param string $opponent
     * @param string $table
     * @param FilterInterface[]|callable[] $filters
     */
    public function __construct($class, array $reference, $opponent, $table, array $filters = [])
    {
        $this->class = $class;
        $this->opponent = $opponent;
        $this->reference = $reference;
        $this->table = $table;
        $this->filters = $filters;
    }

    /** {@inheritDoc} */
    public static function fromShort($parent, array $short)
    {
        // remove cardinality if given
        if ($short[0] === self::CARDINALITY_MANY) {
            array_shift($short);
        }

        // get filters
        $filters = [];
        if (count($short) === 5 && is_array($short[4])) {
            $filters = array_pop($short);
        }

        if (count($short) === 4 &&
            is_string($short[0]) && is_array($short[1]) && is_string($short[2]) && is_string($short[3])
        ) {
            return new self($short[0], $short[1], $short[2], $short[3], $filters);
        }
        return null;
    }

    /** {@inheritDoc} */
    protected static function fromAssoc($parent, array $relDef)
    {
        $class       = isset($relDef[self::OPT_CLASS]) ? $relDef[self::OPT_CLASS] : null;
        $reference   = isset($relDef[self::OPT_REFERENCE]) ? $relDef[self::OPT_REFERENCE] : null;
        $table       = isset($relDef[self::OPT_TABLE]) ? $relDef[self::OPT_TABLE] : null;
        $opponent    = isset($relDef[self::OPT_OPPONENT]) ? $relDef[self::OPT_OPPONENT] : null;
        $filters     = isset($relDef[self::OPT_FILTERS]) ? $relDef[self::OPT_FILTERS] : [];

        if ($class && $reference && $opponent && $table) {
            return new ManyToMany($class, $reference, $opponent, $table, $filters);
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
        $table = $entityManager->escapeIdentifier($this->table);
        $fetcher = $this->createFetcher($entityManager);

        $foreignKey = Helper::getKey($this->reference, $self, false);
        foreach ($foreignKey as $col => $value) {
            $fetcher->where($table . '.' . $entityManager->escapeIdentifier($col), $value);
        }

        foreach ($this->filters as $filter) {
            $fetcher->filter($filter);
        }
        return $fetcher;
    }

    public function eagerLoad(EntityManager $em, Entity ...$entities)
    {
        $opponent = $this->getOpponent(ManyToMany::class);

        $attributes = array_keys($this->reference);
        $fkColumns = array_values($this->reference);
        $opFkColumns = array_values($opponent->reference);
        $opAttributes = array_keys($opponent->reference);

        $mappingData = $this->getMappingData($em, ...$entities);

        $foreignObjects = $em->fetch($this->class)
            ->whereIn($opAttributes, Helper::uniqueArrays(array_map(function ($mappingRow) use ($opFkColumns) {
                return Helper::only($mappingRow, $opFkColumns);
            }, $mappingData)))
            ->all();

        // assign foreign objects to $entities
        $foreignObjects = Helper::keyBy($foreignObjects, $opAttributes);
        $mapping = Helper::groupBy($mappingData, $fkColumns);
        foreach (Helper::groupBy($entities, $attributes) as $key => $entities) {
            foreach ($entities as $entity) {
                $entity->setCurrentRelated(
                    $this->name,
                    isset($mapping[$key]) ?
                        array_values(Helper::only($foreignObjects, Helper::pluck($mapping[$key], $opFkColumns))) :
                        []
                );
            }
        }
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
                throw new InvalidRelation(sprintf(
                    "Invalid entity for relation %s of entity %s",
                    $this->name,
                    $this->parent
                ));
            }

            $association = $baseAssociation;
            foreach ($this->getOpponent(ManyToMany::class)->reference as $hisVar => $fkCol) {
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
                throw new InvalidRelation(sprintf(
                    "Invalid entity for relation %s of entity %s",
                    $this->name,
                    $this->parent
                ));
            }

            $condition = [];
            foreach ($this->getOpponent(ManyToMany::class)->reference as $hisVar => $fkCol) {
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
        foreach ($this->getOpponent(ManyToMany::class)->reference as $hisVar => $col) {
            $expression[] = $table . '.' . $fetcher->getEntityManager()->escapeIdentifier($col) .
                            ' = ' . $this->name . '.' . $hisVar;
        }

        call_user_func([ $fetcher, $join ], $this->class, implode(' AND ', $expression), $this->name, [], true);
    }

    /**
     * @param EntityManager $entityManager
     * @return EntityFetcher
     */
    protected function createFetcher(EntityManager $entityManager)
    {
        $table = $entityManager->escapeIdentifier($this->table);
        $opponent = $this->getOpponent(ManyToMany::class);
        /** @var EntityFetcher $fetcher */
        $fetcher = $entityManager->fetch($this->class);

        $expression = [];
        foreach ($opponent->reference as $t0Var => $fkCol) {
            $expression[] = $table . '.' . $entityManager->escapeIdentifier($fkCol) . ' = t0.' . $t0Var;
        }

        $fetcher->join($table, implode(' AND ', $expression));
        return $fetcher;
    }

    /**
     * @param EntityManager $em
     * @param Entity[] $entities
     * @return array
     */
    protected function getMappingData(EntityManager $em, Entity ...$entities)
    {
        $query = $em->query($em->escapeIdentifier($this->table), 't0')
            ->whereIn(array_map(function ($col) use ($em) {
                return 't0.' . $em->escapeIdentifier($col);
            }, array_values($this->reference)), Helper::getUniqueKeys($this->reference, ...$entities));
        return $query->all();
    }
}
