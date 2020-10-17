<?php

namespace ORM\EntityFetcher;

use ORM\Relation;

trait MakesJoins
{
    use \ORM\QueryBuilder\MakesJoins;

    public function join($class, $expression = '', $alias = '', $args = [])
    {
        list($table, $alias) = $this->getTableAndAlias($class, $alias);
        return parent::join($table, $expression, $alias, $args);
    }

    public function leftJoin($class, $expression = '', $alias = '', $args = [])
    {
        list($table, $alias) = $this->getTableAndAlias($class, $alias);
        return parent::leftJoin($table, $expression, $alias, $args);
    }

    public function rightJoin($class, $expression = '', $alias = '', $args = [])
    {
        list($table, $alias) = $this->getTableAndAlias($class, $alias);
        return parent::rightJoin($table, $expression, $alias, $args);
    }

    public function fullJoin($class, $expression = '', $alias = '', $args = [])
    {
        list($table, $alias) = $this->getTableAndAlias($class, $alias);
        return parent::fullJoin($table, $expression, $alias, $args);
    }

    /**
     * Create the join with $join type
     *
     * @param $join
     * @param $relation
     * @return $this
     */
    public function createRelatedJoin($join, $relation)
    {
        if (strpos($relation, '.') !== false) {
            list($alias, $relation) = explode('.', $relation);
            $class = $this->classMapping['byAlias'][$alias];
        } else {
            $class = $this->class;
            $alias = $this->alias;
        }

        /** @var Relation $relation */
        $relation = call_user_func([$class, 'getRelation'], $relation);
        $relation->addJoin($this, $join, $alias);
        return $this;
    }

    /**
     * Join $relation
     *
     * @param $relation
     * @return $this
     */
    public function joinRelated($relation)
    {
        return $this->createRelatedJoin('join', $relation);
    }

    /**
     * Left outer join $relation
     *
     * @param $relation
     * @return $this
     */
    public function leftJoinRelated($relation)
    {
        return $this->createRelatedJoin('leftJoin', $relation);
    }
}
