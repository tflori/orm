<?php

namespace ORM;

use ORM\Dbal\Dbal;
use ORM\Exception\InvalidArgument;

class BulkInsert
{
    /** @var string */
    protected $class;

    /** @var Dbal */
    protected $dbal;

    /** @var int */
    protected $limit;

    /** @var callable */
    protected $onSync;

    /** @var bool */
    protected $useAutoIncrement;

    /** @var Entity[] */
    protected $new = [];

    /** @var Entity[] */
    protected $synced = [];

    /**
     * BulkInsert constructor.
     *
     * @param string $class
     * @param Dbal $dbal
     * @param int $limit
     * @param callable $onSync
     * @param bool $useAutoIncrement
     */
    public function __construct(Dbal $dbal, $class, $useAutoIncrement = true, $limit = 20, callable $onSync = null)
    {
        $this->class = $class;
        $this->dbal = $dbal;
        $this->limit = $limit;
        $this->onSync = $onSync;
        $this->useAutoIncrement = $useAutoIncrement;
    }

    /**
     * Add an entity to the bulk insert.
     *
     * @param Entity ...$entities
     * @throws InvalidArgument
     */
    public function add(Entity ...$entities)
    {
        foreach ($entities as $entity) {
            if (!$entity instanceof $this->class) {
                throw new InvalidArgument('Only entities from type ' . $this->class . ' can be added');
            }
        }

        array_push($this->new, ...$entities);
        while (count($this->new) >= $this->limit) {
            $this->execute();
        }
    }

    /**
     * Insert the outstanding entities and return all synced objects.
     *
     * @return Entity[]
     */
    public function finish()
    {
        if (!empty($this->new)) {
            $this->execute();
        }
        return $this->synced;
    }

    /**
     * Executes the bulk insert.
     */
    protected function execute()
    {
        $new = array_splice($this->new, 0, $this->limit);
        if ($this->dbal->bulkInsert($new, $this->useAutoIncrement)) {
            array_push($this->synced, ...$new);
            !$this->onSync || call_user_func($this->onSync, $new);
        }
    }
}
