<?php

namespace ORM\Testing;

use ORM\EntityFetcher;
use ORM\Testing\EntityFetcherMock\Result;

class EntityFetcherMock extends EntityFetcher
{
    /** @var Result|null */
    protected $matchedResult;

    /** @var EntityManagerMock */
    public $entityManager;

    /** @return Result|null */
    protected function match()
    {
        if ($this->matchedResult === null) {
            $this->matchedResult = $this->entityManager->getMatchedResult($this->class, $this);
        }
        return $this->matchedResult;
    }

    /**
     * Get the matched result if it expects $method
     *
     * @param string $method
     * @return Result|null
     */
    protected function matchExpecting($method)
    {
        $result = $this->match();
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
        $entities = $result ? $result->all($limit, $this) : [];

        foreach ($this->eagerLoad as $relation) {
            $this->entityManager->eagerLoad($relation, ...$entities);
        }

        return $entities;
    }

    /** {@inheritDoc} */
    public function count()
    {
        $result = $this->match();
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
