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
    protected $useAutoIncrement = true;

    /** @var bool */
    protected $update = true;

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
     */
    public function __construct(Dbal $dbal, $class, $limit = 20)
    {
        $this->class = $class;
        $this->dbal = $dbal;
        $this->limit = $limit;
        if (!$class::isAutoIncremented()) {
            $this->useAutoIncrement = false;
        }
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

        if (!$this->update) {
            $success = $this->dbal->insert(...$new);
        } elseif ($this->useAutoIncrement) {
            $success = $this->dbal->insertAndSyncWithAutoInc(...$new);
        } else {
            $success = $this->dbal->insertAndSync(...$new);
        }

        if ($success) {
            array_push($this->synced, ...$new);
            !$this->onSync || call_user_func($this->onSync, $new);
        }
    }

    /** @return int
     * @codeCoverageIgnore trivial */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Limit the amount of entities inserted at once.
     *
     * @param int $limit
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Enable updating the primary keys from autoincrement
     *
     * **Caution**: Your db access layer (DBAL) may not support this feature.
     *
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function useAutoincrement()
    {
        $this->useAutoIncrement = true;
        return $this;
    }

    /**
     * Disable updating the primary key by auto increment.
     *
     * **Caution**: If this is disabled updating could cause a IncompletePrimaryKey exception.
     *
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function noAutoIncrement()
    {
        $this->useAutoIncrement = false;
        return $this;
    }

    /**
     * Executes $callback after insert
     *
     * Provides an array of the just inserted entities in first argument.
     *
     * @param callable $callback
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function onSync(callable $callback = null)
    {
        $this->onSync = $callback;
        return $this;
    }

    /**
     * Disable updating entities after insert
     *
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function noUpdates()
    {
        $this->update = false;
        return $this;
    }

    /**
     * Enable updating entities after insert
     *
     * **Caution**: This option will need to update the primary key by autoincrement which maybe is not supported
     * by your db access layer (DBAL).
     *
     * @return $this
     * @codeCoverageIgnore trivial
     */
    public function updateEntities()
    {
        $this->update = true;
        return $this;
    }
}
