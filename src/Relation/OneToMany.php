<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;
use ORM\EntityManager;
use ORM\Exception\InvalidConfiguration;
use ORM\Helper;
use ORM\Relation;

/**
 * OneToMany Relation
 *
 * @package ORM\Relation
 * @author  Thomas Flori <thflori@gmail.com>
 */
class OneToMany extends Relation
{
    use HasOpponent;

    /**
     * Owner constructor.
     *
     * @param string $class
     * @param string $opponent
     * @param FilterInterface[]|callable[] $filters
     */
    public function __construct($class, $opponent, array $filters = [])
    {
        $this->class = $class;
        $this->opponent = $opponent;
        $this->filters = $filters;
    }

    /** {@inheritDoc} */
    public static function fromShort(array $short)
    {
        if ($short[0] === self::CARDINALITY_ONE) {
            return null;
        } elseif ($short[0] === self::CARDINALITY_MANY) {
            array_shift($short);
        }

        return static::createStaticFromShort($short);
    }

    /** {@inheritDoc} */
    protected static function fromAssoc(array $relDef)
    {
        if (isset($relDef[self::OPT_CARDINALITY]) && $relDef[self::OPT_CARDINALITY] === self::CARDINALITY_ONE) {
            return null;
        }

        return self::createStaticFromAssoc($relDef);
    }

    /**
     * Create static::class from $short
     *
     * @param array $short
     * @return static|null
     */
    protected static function createStaticFromShort(array $short)
    {
        // get filters
        $filters = [];
        if (count($short) === 3 && is_array($short[2])) {
            $filters = array_pop($short);
        }

        if (count($short) === 2 && is_string($short[0]) && is_string($short[1])) {
            return new static($short[0], $short[1], $filters);
        }
        return null;
    }

    /**
     * Create static::class from $relDef
     *
     * @param array $relDef
     * @return static|null
     */
    protected static function createStaticFromAssoc(array $relDef)
    {
        $class = isset($relDef[self::OPT_CLASS]) ? $relDef[self::OPT_CLASS] : null;
        $opponent = isset($relDef[self::OPT_OPPONENT]) ? $relDef[self::OPT_OPPONENT] : null;
        $filters = isset($relDef[self::OPT_FILTERS]) ? $relDef[self::OPT_FILTERS] : [];

        if ($class && $opponent && !isset($relDef['table'])) {
            return new static($class, $opponent, $filters);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfiguration
     */
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        $owner = $this->getOpponent(Owner::class);
        /** @var EntityFetcher $fetcher */
        $fetcher = $entityManager->fetch($this->class);
        $owner->apply($fetcher, $self);

        foreach ($this->filters as $filter) {
            $fetcher->filter($filter);
        }
        return $fetcher;
    }

    /** {@inheritDoc} */
    public function eagerLoad(EntityManager $em, Entity ...$entities)
    {
        $foreignObjects = $this->getOpponent(Owner::class)->eagerLoadSelf($em, ...$entities);
        foreach ($entities as $entity) {
            $key = spl_object_hash($entity);
            $entity->setCurrentRelated($this->name, isset($foreignObjects[$key]) ? $foreignObjects[$key] : []);
        }
    }

    /** {@inheritdoc} */
    public function addJoin(EntityFetcher $fetcher, $join, $alias)
    {
        $owner = $this->getOpponent(Owner::class);
        $parenthesis = call_user_func([$fetcher, $join], $this->class, false, $this->name);
        $owner->applyJoin($parenthesis, $alias, $this);
        $parenthesis->close();
    }
}
