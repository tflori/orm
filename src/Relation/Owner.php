<?php

namespace ORM\Relation;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\EntityManager;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\InvalidRelation;
use ORM\QueryBuilder\Parenthesis;
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
     * @param string $class
     * @param array  $reference
     */
    public function __construct($class, array $reference)
    {
        $this->class     = $class;
        $this->reference = $reference;
    }

    /** {@inheritDoc} */
    public static function fromShort(array $short)
    {
        if ($short[0] === self::CARDINALITY_ONE) {
            array_shift($short);
        }

        if (count($short) === 2 && is_string($short[0]) && is_array($short[1])) {
            return new self($short[0], $short[1]);
        }
        return null;
    }

    /** {@inheritDoc} */
    protected static function fromAssoc(array $relDef)
    {
        $class       = isset($relDef[self::OPT_CLASS]) ? $relDef[self::OPT_CLASS] : null;
        $reference   = isset($relDef[self::OPT_REFERENCE]) ? $relDef[self::OPT_REFERENCE] : null;

        if ($class && $reference && !isset($relDef[self::OPT_TABLE])) {
            return new Owner($class, $reference);
        }
        return null;
    }

    /** {@inheritdoc} */
    public function fetch(Entity $self, EntityManager $entityManager)
    {
        $key = array_map([$self, 'getAttribute' ], array_keys($this->reference));

        if (in_array(null, $key)) {
            return null;
        }

        return $entityManager->fetch($this->class, $key);
    }

    /**
     * Apply where conditions for $entity on $fetcher
     *
     * Called from non-owner to find related elements. Example:
     *   $user->fetch('articles') creates an EntityFetcher for Article and calls
     *     $opponent->apply($fetcher, $user) that will call
     *       $fetcher->where('authorId', $user->id)
     *
     * @param EntityFetcher $fetcher
     * @param Entity $entity
     */
    public function apply(EntityFetcher $fetcher, Entity $entity)
    {
        $foreignKey = $this->getForeignKey($entity, array_flip($this->reference));
        foreach ($foreignKey as $col => $value) {
            $fetcher->where($col, $value);
        }
    }

    /**
     * Adds the join clause to the entity fetcher
     *
     * @param Parenthesis $join
     * @param string $yourAlias
     * @param OneToMany $opponent
     */
    public function applyJoin(Parenthesis $join, $yourAlias, OneToMany $opponent)
    {
        foreach ($this->reference as $myColumn => $yourColumn) {
            $join->where(sprintf("%s.%s = %s.%s", $yourAlias, $yourColumn, $opponent->name, $myColumn));
        }
    }

    /**
     * {@inheritdoc}
     * @throws InvalidRelation
     * @throws IncompletePrimaryKey
     */
    public function setRelated(Entity $self, Entity $entity = null)
    {
        if ($entity !== null && !$entity instanceof $this->class) {
            throw new InvalidRelation(sprintf(
                "Invalid entity for relation %s of entity %s",
                $this->name,
                $this->parent
            ));
        }

        foreach ($this->reference as $fkAttribute => $attribute) {
            if ($entity === null) {
                $self->setAttribute($fkAttribute, null);
                continue;
            }

            $value = $entity->getAttribute($attribute);

            if ($value === null) {
                throw new IncompletePrimaryKey('Key incomplete to save foreign key');
            }

            $self->setAttribute($fkAttribute, $value);
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
