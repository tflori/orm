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
     * @var array|Relation[] */
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
     * @param string $name
     * @return Relation
     * @throws UndefinedRelation
     */
    public static function getRelation($name)
    {
        static::bootIfNotBooted();

        $em = EM::getInstance(static::class);
        $method = $em->getNamer()->getMethodName($name . 'Relation', self::$namingSchemeMethods);

        if (!isset(static::$relations[$name])) {
            if (!method_exists(static::class, $method)) {
                throw new UndefinedRelation('Relation ' . $name . ' is not defined');
            }
            $relation = call_user_func([static::class, $method]);
            static::$relations[$name] = $relation;
        }

        if (!static::$relations[$name] instanceof Relation) {
            $relation = Relation::createRelation(static::class, $name, static::$relations[$name]);
            static::$relations[$name] = $relation;
        }

        static::$relations[$name]->bind(static::class, $name);
        return static::$relations[$name];
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
        if ($refresh || !array_key_exists($relation, $this->relatedObjects)) {
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
     * Load the related objects of $relation
     *
     * Nested relations can be loaded by separating them by "." for example load all articles with comments from
     * a user ($this): `$user->load('articles.comments')`.
     *
     * @param string $relation
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function load($relation)
    {
        $this->entityManager->eagerLoad($relation, $this);
        return $this;
    }

    /**
     * Check if $relation got loaded already
     *
     * @param string $relation
     * @return bool
     */
    public function hasLoaded($relation)
    {
        return array_key_exists($relation, $this->relatedObjects);
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
