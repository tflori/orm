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
        return $result ? $result->one() : null;
    }

    /** {@inheritDoc} */
    public function all($limit = 0)
    {
        $result = $this->match();
        return $result ? $result->all($limit) : [];
    }

    /** {@inheritDoc} */
    public function count()
    {
        $result = $this->match();
        return $result ? $result->count() : 0;
    }

    /** {@inheritDoc} */
    public function delete()
    {
        $result = $this->match();
        return $result ? $result->delete() : 0;
    }

    /** {@inheritDoc} */
    public function update(array $updates)
    {
        $result = $this->match();
        return $result ? $result->update($updates) : 0;
    }

    /** {@inheritDoc} */
    public function insert(array ...$rows)
    {
        $result = $this->match();
        return $result ? $result->insert(...$rows) : 0;
    }
}
