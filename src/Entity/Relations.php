<?php /** @noinspection PhpParamsInspection */

namespace ORM\Entity;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager as EM;
use ORM\Exception\UndefinedRelation;
use ORM\Helper;
use ORM\Relation;

trait Relations
{
    /** Relation definitions
     * @var array<string, array|Relation> */
    protected static $relations = [];

    /** Relation definitions from boot() per class
     * @var array<string, array<string, array|Relation>> */
    private static $definedRelations = [];

    /** Resolved relations per class
     * @var array<string, array<string, Relation>> */
    private static $resolvedRelations = [];

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
        $class = Helper::entityClass(static::class);
        if ($class !== static::class) {
            // mocked entities share the relations of the mocked class
            return $class::getRelation($name);
        }

        if (isset(self::$resolvedRelations[static::class][$name])) {
            return self::$resolvedRelations[static::class][$name];
        }

        static::bootIfNotBooted();

        $definition = self::getRelationDefinition($name);
        if ($definition instanceof Relation) {
            // the definition might be shared with other classes
            $relation = clone $definition;
        } else {
            $relation = Relation::createRelation(static::class, $name, $definition);
        }

        $relation->bind(static::class, $name);
        return self::$resolvedRelations[static::class][$name] = $relation;
    }

    /**
     * Define $relation for this class
     *
     * Use this method in boot() instead of writing to static::$relations, which might be shared with other entities.
     *
     * @param string $name
     * @param array|Relation $definition
     */
    protected static function defineRelation($name, $definition)
    {
        self::$definedRelations[static::class][$name] = $definition;
    }

    /**
     * Check if $name is a defined relation
     *
     * @param string $name
     * @return bool
     */
    public static function hasRelation($name)
    {
        $class = Helper::entityClass(static::class);
        if ($class !== static::class) {
            return $class::hasRelation($name);
        }

        if (isset(self::$resolvedRelations[static::class][$name])) {
            return true;
        }

        static::bootIfNotBooted();

        return self::getDefinedRelation($name) !== null ||
            isset(static::$relations[$name]) ||
            self::getRelationMethod($name) !== null;
    }

    /**
     * Get the definition for $name
     *
     * Definitions from boot() take precedence over static::$relations, which take precedence over <name>Relation()
     * methods.
     *
     * @param string $name
     * @return array|Relation
     * @throws UndefinedRelation
     */
    private static function getRelationDefinition($name)
    {
        if (($definition = self::getDefinedRelation($name)) !== null) {
            return $definition;
        }

        if (isset(static::$relations[$name])) {
            return static::$relations[$name];
        }

        if (!$method = self::getRelationMethod($name)) {
            throw new UndefinedRelation('Relation ' . $name . ' is not defined');
        }

        return call_user_func([static::class, $method]);
    }

    /**
     * Get the definition for $name from boot()
     *
     * The definitions of parent classes are inherited through parent::boot().
     *
     * @param string $name
     * @return array|Relation|null
     */
    private static function getDefinedRelation($name)
    {
        return isset(self::$definedRelations[static::class][$name]) ?
            self::$definedRelations[static::class][$name] : null;
    }

    /**
     * Get the name of the <name>Relation() method if it exists
     *
     * @param string $name
     * @return string|null
     */
    private static function getRelationMethod($name)
    {
        $method = EM::getInstance(static::class)->getNamer()
            ->getMethodName($name . 'Relation', self::$namingSchemeMethods);

        return method_exists(static::class, $method) ? $method : null;
    }

    /**
     * Move the relations defined in boot() to the definitions of this class
     *
     * static::$relations is shared with all entities that don't redeclare it - so we restore it after booting.
     * Relations from defineRelation() take precedence (e.g. a subclass overriding a relation of its parent's boot()).
     *
     * @param array $relationsBeforeBoot
     * @internal
     */
    protected static function storeRelationsFromBoot(array $relationsBeforeBoot)
    {
        foreach (static::$relations as $name => $definition) {
            if (isset(self::$definedRelations[static::class][$name])) {
                continue;
            }
            if (!array_key_exists($name, $relationsBeforeBoot) || $relationsBeforeBoot[$name] !== $definition) {
                self::$definedRelations[static::class][$name] = $definition;
            }
        }
        static::$relations = $relationsBeforeBoot;
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
     * @param string  $relation
     * @param ?Entity $entity
     */
    public function setRelated($relation, ?Entity $entity = null)
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
        // @phpstan-ignore-next-line booleanAnd.alwaysFalse, instanceof.alwaysFalse, booleanOr.alwaysFalse
        if ($getAll instanceof EM || func_num_args() === 3 && $getAll === null) {
            // @phpstan-ignore-next-line identical.alwaysTrue
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
    public function hasLoaded(string $relation): bool
    {
        return array_key_exists($relation, $this->relatedObjects);
    }

    /**
     * Resets all loaded relations or $relation
     *
     * Helpful to reduce the size of serializations of the object (for caching, or toArray method etc.)
     *
     * @param ?string $relation
     */
    public function resetRelated(?string $relation = null): void
    {
        if ($relation === null) {
            $this->relatedObjects = [];
        } else {
            unset($this->relatedObjects[$relation]);
        }
    }
}
