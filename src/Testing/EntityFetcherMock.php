<?php

namespace ORM\Testing;

use ORM\EntityFetcher;

class EntityFetcherMock extends EntityFetcher
{
    /** @var array */
    protected $currentResult;

    /** @var EntityManagerMock */
    public $entityManager;

    /** {@inheritDoc} */
    public function one()
    {
        if ($this->currentResult === null) {
            $this->currentResult = $this->entityManager->getResults($this->class, $this);
        }

        return array_shift($this->currentResult);
    }

    /** {@inheritDoc} */
    public function count()
    {
        return count($this->entityManager->getResults($this->class, $this));
    }
}
