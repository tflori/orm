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

    /** @var string */
    private $query;

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
        $result = $this->getStatement();
        if (!$result) {
            return null;
        }

        $data      = $result->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

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
     * @return \PDOStatement
     */
    private function getStatement()
    {
        if ($this->result === null) {
            $c            = $this->class;
            $this->result = $this->entityManager->getConnection($c::$connection)->query($this->getQuery());
        }
        return $this->result;
    }

    private function getQuery()
    {
        $c = $this->class;
        $base = 'SELECT t0.* FROM ' . $c::getTableName() . ' AS t0';

        if ($this->query) {
            return preg_replace(
                '/.*SELECT .* FROM .* ((INNER|LEFT|JOIN|WHERE|RIGHT|OUTER).*)/ism',
                $base . ' $1',
                $this->query
            );
        }

        return $base;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }
}
