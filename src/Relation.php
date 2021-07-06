<?php

namespace ORM;

use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidConfiguration;
use ORM\Exception\InvalidRelation;
use ORM\Relation\ManyToMany;
use ORM\Relation\Morphed;
use ORM\Relation\OneToMany;
use ORM\Relation\OneToOne;
use ORM\Relation\Owner;
use ORM\Relation\ParentChildren;

/**
 * Base Relation
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
abstract class Relation
{
    const OPT_CLASS        = 'class';
    const OPT_MORPH_COLUMN = 'morphColumn';
    const OPT_MORPH        = 'morph';
    const OPT_REFERENCE    = 'reference';
    const OPT_CARDINALITY  = 'cardinality';
    const OPT_OPPONENT     = 'opponent';
    const OPT_TABLE        = 'table';
    const OPT_FILTERS      = 'filters';
    const CARDINALITY_ONE  = 'one';
    const CARDINALITY_MANY = 'many';

    /** The parent entity that defined this relation
     * @var string */
    protected $parent;

    /** The name of the relation for error messages
     * @var string */
    protected $name;

    /** The class that is related
     * @var string */
    protected $class;

    /** The name of the relation in the related class
     * @var string */
    protected $opponent;

    /** Reference definition as key value pairs
     * @var array */
    protected $reference;

    /** Filters applied to all fetchers
     * @var array */
    protected $filters = [];

    /**
     * Factory for relation definition object
     *
     * @param string $parent
     * @param string $name
     * @param array $relDef
     * @return Relation
     * @throws InvalidConfiguration
     */
    public static function createRelation($parent, $name, $relDef)
    {
        /** @var string[]|Relation[] $relationClasses */
        $relationClasses = [
            Owner::class,
            ManyToMany::class,
            ParentChildren::class,
            OneToMany::class,
            OneToOne::class,
            Morphed::class,
        ];

        if (isset($relDef[0])) {
            foreach ($relationClasses as $relationClass) {
                if ($relation = $relationClass::fromShort($parent, $relDef)) {
                    return $relation;
                }
            }
            throw new InvalidConfiguration('Invalid short form for relation ' . $name . ' for entity ' . $parent);
        }

        foreach ($relationClasses as $relationClass) {
            if ($relation = $relationClass::fromAssoc($parent, $relDef)) {
                return $relation;
            }
        }

        throw new InvalidConfiguration(sprintf("Invalid relation %s for entity %s", $name, $parent));
    }

    /**
     * Creates a relation from short form
     *
     * @param string $parent
     * @param array $short
     * @return ?Relation
     * @codeCoverageIgnore
     *@see Relation::createRelation()
     * @internal
     */
    public static function fromShort($parent, array $short)
    {
        return null;
    }

    /**
     * Create a relation by assoc definition
     *
     * @param string $parent
     * @param array $relDef
     * @return ?Relation
     * @codeCoverageIgnore
     *@see Relation::createRelation()
     * @internal
     */
    protected static function fromAssoc($parent, array $relDef)
    {
        return null;
    }

    /**
     * Bind this relation to class $parent with $name
     *
     * @param string $parent
     * @param string $name
     * @internal
     */
    public function bind($parent, $name)
    {
        if ($this->name || $this->parent) {
            $this->checkBoundTo($parent, $name);
            return;
        }
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * Check if the relation is bound to $parent and throw if not
     *
     * @throws Exception
     */
    protected function checkBoundTo($parent, $name)
    {
        if ($this->parent !== $parent) {
            $reflection = new \ReflectionClass($parent);
            if ($reflection->isSubclassOf($this->parent)) {
                $parent = $this->parent;
            }
        }

        if ($this->parent !== $parent || $this->name !== $name) {
            throw new Exception(sprintf(
                'Relation already used for %s on entity %s',
                $this->name,
                $this->parent
            ));
        }
    }

    /**
     * Fetch the relation
     *
     * Runs fetch on the EntityManager and returns its result.
     *
     * @param Entity        $self
     * @param EntityManager $entityManager
     * @return mixed
     */
    abstract public function fetch(Entity $self, EntityManager $entityManager);

    /**
     * Fetch all from the relation
     *
     * Runs fetch and returns EntityFetcher::all() if a Fetcher is returned.
     *
     * @param Entity        $self
     * @param EntityManager $entityManager
     * @return Entity[]|Entity
     */
    public function fetchAll(Entity $self, EntityManager $entityManager)
    {
        $fetcher = $this->fetch($self, $entityManager);

        if ($fetcher instanceof EntityFetcher) {
            return $fetcher->all();
        }

        return $fetcher;
    }

    /**
     * Set the relation to $entity
     *
     * @param Entity $self
     * @param Entity|null $entity
     * @throws InvalidRelation
     */
    public function setRelated(Entity $self, Entity $entity = null)
    {
        throw new InvalidRelation('This is not the owner of the relation');
    }

    /**
     * Add $entities to association table
     *
     * @param Entity $self
     * @param Entity[] $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function addRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        throw new InvalidRelation('This is not a many-to-many relation');
    }

    /**
     * Delete $entities from association table
     *
     * @param Entity $self
     * @param Entity[] $entities
     * @param EntityManager $entityManager
     * @throws InvalidRelation
     */
    public function deleteRelated(Entity $self, array $entities, EntityManager $entityManager)
    {
        throw new InvalidRelation('This is not a many-to-many relation');
    }

    /**
     * Load this relation for all $entities with one query
     *
     * @param Entity ...$entities
     * @codeCoverageIgnore trivial
     */
    public function eagerLoad(EntityManager $em, Entity ...$entities)
    {
        throw new InvalidRelation('Eager loading not supported for relation '. $this->name);
    }

    /**
     * Join this relation in $fetcher
     *
     * @param EntityFetcher $fetcher
     * @param string        $join
     * @param string        $alias
     * @internal Used only from EntityFetcher
     * @return mixed
     */
    abstract public function addJoin(EntityFetcher $fetcher, $join, $alias);

    /**
     * Assign $foreignObjects to $entities mapped by attributes
     *
     * @param array $fkAttributes
     * @param array $keyAttributes
     * @param array $entities
     * @param array $foreignObjects
     */
    protected function assignForeignObjects(
        array $fkAttributes,
        array $keyAttributes,
        array $entities,
        array $foreignObjects
    ) {
        $foreignObjects = Helper::keyBy($foreignObjects, $keyAttributes);
        $entityGroups = Helper::groupBy($entities, $fkAttributes);
        foreach ($entityGroups as $key => $entities) {
            foreach ($entities as $entity) {
                $entity->setCurrentRelated($this->name, isset($foreignObjects[$key]) ? $foreignObjects[$key] : null);
            }
        }
    }
}
