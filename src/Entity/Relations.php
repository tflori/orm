<?php /** @noinspection PhpParamsInspection */

namespace ORM\Entity;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager as EM;
use ORM\Exception\UndefinedRelation;
use ORM\Relation;

trait Relations
{
    /** Relation definitions
     * @var array */
    protected static $relations = [];

    /** The entity manager from which this entity got created
     * @var EM */
    protected $entityManager;

    /** Related objects for getRelated
     * @var array */
    protected $relatedObjects = [];

    /**
     * Get the definition for $relation
     *
     * It normalize the short definition form and create a Relation object from it.
     *
     * @param string $relation
     * @return Relation
     * @throws UndefinedRelation
     */
    public static function getRelation($relation)
    {
        if (!isset(static::$relations[$relation])) {
            throw new UndefinedRelation('Relation ' . $relation . ' is not defined');
        }

        $relDef = &static::$relations[$relation];

        if (!$relDef instanceof Relation) {
            $relDef = Relation::createRelation($relation, $relDef);
        }

        return $relDef;
    }

    /**
     * Get related objects
     *
     * The difference between getRelated and fetch is that getRelated stores the fetched entities. To refresh set
     * $refresh to true.
     *
     * @param string $relation
     * @param bool   $refresh
     * @return mixed
     */
    public function getRelated($relation, $refresh = false)
    {
        if ($refresh || !isset($this->relatedObjects[$relation])) {
            $this->relatedObjects[$relation] = $this->fetch($relation, true);
        }

        return $this->relatedObjects[$relation];
    }

    /**
     * Set $relation to $entity
     *
     * This method is only for the owner of a relation.
     *
     * @param string $relation
     * @param Entity $entity
     */
    public function setRelated($relation, Entity $entity = null)
    {
        $this::getRelation($relation)->setRelated($this, $entity);

        $this->relatedObjects[$relation] = $entity;
    }

    /**
     * Add relations for $relation to $entities
     *
     * This method is only for many-to-many relations.
     *
     * This method does not take care about already existing relations and will fail hard.
     *
     * @param string $relation
     * @param Entity[] $entities
     */
    public function addRelated($relation, array $entities)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 3 && func_get_arg(2) instanceof EM) {
            trigger_error(
                'Passing EntityManager to addRelated is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $this::getRelation($relation)->addRelated($this, $entities, $this->entityManager);
    }

    /**
     * Delete relations for $relation to $entities
     *
     * This method is only for many-to-many relations.
     *
     * @param string $relation
     * @param Entity[] $entities
     */
    public function deleteRelated($relation, $entities)
    {
        // @codeCoverageIgnoreStart
        if (func_num_args() === 3 && func_get_arg(2) instanceof EM) {
            trigger_error(
                'Passing EntityManager to deleteRelated is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $this::getRelation($relation)->deleteRelated($this, $entities, $this->entityManager);
    }

    /**
     * Fetches related objects
     *
     * For relations with cardinality many it returns an EntityFetcher. Otherwise it returns the entity.
     *
     * It will throw an error for non owner when the key is incomplete.
     *
     * @param string $relation The relation to fetch
     * @param bool $getAll
     * @return Entity|Entity[]|EntityFetcher
     */
    public function fetch($relation, $getAll = false)
    {
        // @codeCoverageIgnoreStart
        if ($getAll instanceof EM || func_num_args() === 3 && $getAll === null) {
            $getAll = func_num_args() === 3 ? func_get_arg(2) : false;
            trigger_error(
                'Passing EntityManager to fetch is deprecated. Use ->setEntityManager() to overwrite',
                E_USER_DEPRECATED
            );
        }
        // @codeCoverageIgnoreEnd

        $relation = $this::getRelation($relation);

        return $getAll ? $relation->fetchAll($this, $this->entityManager) :
            $relation->fetch($this, $this->entityManager);
    }

    /**
     * Resets all loaded relations or $relation
     *
     * Helpful to reduce the size of serializations of the object (for caching, or toArray method etc.)
     *
     * @param null $relation
     */
    public function resetRelated($relation = null)
    {
        if ($relation === null) {
            $this->relatedObjects = [];
        } else {
            unset($this->relatedObjects[$relation]);
        }
    }
}
