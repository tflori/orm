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
    private function match()
    {
        if ($this->matchedResult === null) {
            $this->matchedResult = $this->entityManager->getMatchedResult($this->class, $this);
        }
        return $this->matchedResult;
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
        $result = $this->match();
        return $result && $result->isExpected('delete') ? $result->delete() : parent::delete();
    }

    /**
     * {@inheritDoc}
     *
     * Executes the statement on the (mocked) connection unless the matched result expects update().
     */
    public function update(array $updates)
    {
        $result = $this->match();
        return $result && $result->isExpected('update') ? $result->update($updates) : parent::update($updates);
    }

    /**
     * {@inheritDoc}
     *
     * Executes the statement on the (mocked) connection unless the matched result expects insert().
     */
    public function insert(array ...$rows)
    {
        $result = $this->match();
        return $result && $result->isExpected('insert') ? $result->insert(...$rows) : parent::insert(...$rows);
    }
}
