<?php

namespace ORM\Testing;

use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Testing\EntityFetcherMock\Result;

class EntityFetcherMock extends EntityFetcher
{
    /** @var Result|null */
    protected $matchedResult;

    /** @var EntityManagerMock */
    public $entityManager;

    /**
     * Get the result matching the query that got executed with one() or all()
     *
     * Like a real fetcher the query gets executed only once - later changes of the query have no effect.
     *
     * @return Result|null
     */
    protected function match()
    {
        if ($this->matchedResult === null) {
            $this->matchedResult = $this->entityManager->getMatchedResult($this->class, $this);
        }
        return $this->matchedResult;
    }

    /**
     * Get the matched result for the current query if it expects $method
     *
     * count(), delete(), update() and insert() execute an own query and therefore match every time.
     *
     * @param string $method
     * @return Result|null
     */
    protected function matchExpecting($method)
    {
        $result = $this->entityManager->getMatchedResult($this->class, $this);
        return $result && $result->isExpected($method) ? $result : null;
    }

    /** {@inheritDoc} */
    public function one()
    {
        $result = $this->match();
        return $result ? $result->one($this) : null;
    }

    /** {@inheritDoc} */
    public function all($limit = 0)
    {
        $result = $this->match();
        $entities = $result ? self::toEntityArray($result->all($limit, $this)) : [];

        foreach ($this->eagerLoad as $relation) {
            $this->entityManager->eagerLoad($relation, ...$entities);
        }

        return $entities;
    }

    /**
     * Convert the return value of the mocked Result::all() to an array
     *
     * An expectation may return anything - e.g. a generator from ->andYield().
     *
     * @param mixed $entities
     * @return Entity[]
     */
    private static function toEntityArray($entities)
    {
        return $entities instanceof \Traversable ? iterator_to_array($entities, false) : $entities;
    }

    /** {@inheritDoc} */
    public function count()
    {
        // count executes an own query - it does not lock the query for one() and all()
        $result = $this->entityManager->getMatchedResult($this->class, $this);
        return $result ? $result->count() : 0;
    }

    /**
     * {@inheritDoc}
     *
     * Executes the statement on the (mocked) connection unless the matched result expects delete().
     */
    public function delete()
    {
        $result = $this->matchExpecting('delete');
        return $result ? $result->delete() : parent::delete();
    }

    /**
     * {@inheritDoc}
     *
     * Executes the statement on the (mocked) connection unless the matched result expects update().
     */
    public function update(array $updates)
    {
        $result = $this->matchExpecting('update');
        return $result ? $result->update($updates) : parent::update($updates);
    }

    /**
     * {@inheritDoc}
     *
     * Executes the statement on the (mocked) connection unless the matched result expects insert().
     */
    public function insert(array ...$rows)
    {
        $result = $this->matchExpecting('insert');
        return $result ? $result->insert(...$rows) : parent::insert(...$rows);
    }
}
