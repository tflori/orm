<?php

namespace ORM;

/**
 * Class EntityFetcher
 *
 * @package ORM
 * @author Thomas Flori <thflori@gmail.com>
 */
class EntityFetcher
{
    /** @var EntityManager */
    private $entityManager;

    /** @var string|Entity */
    private $class;

    /** @var \PDOStatement */
    private $result;

    /**
     * EntityFetcher constructor.
     *
     * @param EntityManager $entityManager
     * @param Entity|string $class
     */
    public function __construct(EntityManager $entityManager, $class)
    {
        $this->entityManager = $entityManager;
        $this->class         = $class;
    }

    public function one()
    {
        $result = $this->getResult();
        if (!$result) {
            return null;
        }

        $data      = $result->fetch(\PDO::FETCH_ASSOC);
        $c         = $this->class;
        $newEntity = new $c($data, true);
        $entity    = $this->entityManager->map($newEntity);

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
     * @return \PDOStatement
     */
    private function getResult()
    {
        if ($this->result === null) {
            $c            = $this->class;
            $this->result = $this->entityManager->getConnection($c::$connection)->query($this->getStatement());
        }
        return $this->result;
    }

    private function getStatement()
    {
        $c = $this->class;
        return 'SELECT t0.* FROM ' . $c::getTableName() . ' AS t0';
    }
}
