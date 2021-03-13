<?php

namespace ORM;

use ORM\EntityFetcher\ExecutesQueries;
use ORM\EntityFetcher\MakesJoins;
use ORM\EntityFetcher\TranslatesClasses;
use ORM\QueryBuilder\QueryBuilder;
use ORM\QueryBuilder\QueryBuilderInterface;
use PDO;

/**
 * Fetch entities from database
 *
 * If you need more specific queries you write them yourself. If you need just more specific where clause you can pass
 * them to the *where() methods.
 *
 * Supported:
 *  - joins with on clause (and alias)
 *  - joins with using (and alias)
 *  - where conditions
 *  - parenthesis
 *  - order by one or more columns / expressions
 *  - group by one or more columns / expressions
 *  - limit and offset
 *  - modifiers
 *
 * @package ORM
 * @author  Thomas Flori <thflori@gmail.com>
 */
class EntityFetcher extends QueryBuilder
{
    use TranslatesClasses;
    use MakesJoins;
    use ExecutesQueries;

    /** The entity class that we want to fetch
     * @var string|Entity */
    protected $class;

    /** The query to execute (overwrites other settings)
     * @var string|QueryBuilderInterface */
    protected $query;

    /** @noinspection PhpMissingParentConstructorInspection */
    /**
     * Constructor
     *
     * @param EntityManager $entityManager EntityManager where to store the fetched entities
     * @param Entity|string $class Class to fetch
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;
        $class::bootIfNotBooted();

        list($this->tableName, $this->alias) = $this->getTableAndAlias($class);
        $this->columns   = [ 't0.*' ];
        $this->modifier  = [ 'DISTINCT' ];
    }

    /**
     * Replaces questionmarks in $expression with $args
     *
     * Additionally this method replaces "ClassName::var" with "alias.col" and "alias.var" with "alias.col" if
     * $translateCols is true (default).
     *
     * @param string      $expression    Expression with placeholders
     * @param array|mixed $args          Argument(s) to insert
     * @param bool        $translateCols Whether or not column names should be translated
     * @return string
     */
    protected function convertPlaceholders($expression, $args, $translateCols = true)
    {
        if ($translateCols) {
            $expression = $this->translateColumn($expression);
        }

        return parent::convertPlaceholders($expression, $args);
    }

    /**
     * {@inheritdoc}
     * @internal
     */
    public function buildWhereInExpression($column, array $values, $inverse = false)
    {
        $column = is_array($column) ? array_map([$this, 'translateColumn'], $column) :
            $this->translateColumn($column);
        return parent::buildWhereInExpression($column, $values, $inverse);
    }

    /**
     * Fetch one entity
     *
     * If there is no more entity in the result set it returns null.
     *
     * @return Entity
     */
    public function one()
    {
        parent::setFetchMode(PDO::FETCH_ASSOC);
        $data = parent::one();
        if (!$data) {
            return null;
        }

        $class = $this->class;
        $newEntity = new $class($data, $this->entityManager, true);
        $entity = $this->entityManager->map($newEntity);

        if ($newEntity !== $entity) {
            $dirty = $entity->isDirty();
            $entity->setOriginalData($data);
            if (!$dirty && $entity->isDirty()) {
                $entity->reset();
            }
        }

        return $entity;
    }

    /**
     * Fetch an array of entities
     *
     * When no $limit is set it fetches all entities in result set.
     *
     * @param int $limit Maximum number of entities to fetch
     * @return Entity[]
     */
    public function all($limit = 0)
    {
        $result = [];

        while ($entity = $this->one()) {
            $result[] = $entity;
            if ($limit && count($result) >= $limit) {
                break;
            }
        }

        return $result;
    }

    /**
     * Get the count of the resulting items
     *
     * @return int
     */
    public function count()
    {
        // set the columns and reset after get query
        $this->columns  = [ 'COUNT(DISTINCT t0.*)' ];
        $this->modifier = [];
        $query          = $this->getQuery();
        $this->columns  = [ 't0.*' ];
        $this->modifier = [ 'DISTINCT' ];

        return (int) $this->entityManager->getConnection()->query($query)->fetchColumn();
    }

    /** {@inheritdoc} */
    public function getQuery()
    {
        if ($this->query) {
            return $this->query instanceof QueryBuilderInterface ? $this->query->getQuery() : $this->query;
        }
        return parent::getQuery();
    }

    /**
     * Set a raw query or use different QueryBuilder
     *
     * For easier use and against sql injection it allows question mark placeholders.
     *
     * @param string|QueryBuilderInterface $query Raw query string or a QueryBuilderInterface
     * @param array                        $args  The arguments for placeholders
     * @return $this
     */
    public function setQuery($query, $args = null)
    {
        if (!$query instanceof QueryBuilderInterface) {
            $query = $this->convertPlaceholders($query, $args, false);
        }

        $this->query = $query;
        return $this;
    }

    /** @return $this
     * @internal */
    public function columns(array $columns = null)
    {
        return $this;
    }

    /** @return $this
     * @internal */
    public function column($column, $args = [], $alias = '')
    {
        return $this;
    }

    /** @return $this
     * @internal */
    public function setFetchMode($mode, $classNameObject = null, array $ctorargs = null)
    {
        return $this;
    }
}
