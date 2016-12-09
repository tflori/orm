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
        if ($this->query) {
            return $this->query;
        }
        $c = $this->class;
        return 'SELECT t0.* FROM ' . $c::getTableName() . ' AS t0';
    }

    public function setQuery($query, array $args = null)
    {
        if (is_array($args) && count($args) === substr_count($query, '?')) {
            $queryParts = explode('?', $query);
            $query = '';
            $c = $this->class;
            foreach ($queryParts as $part) {
                $query .= $part;
                if (count($args)) {
                    $query .= $this->entityManager->queryValue(array_shift($args), $c::$connection);
                }
            }
        }

        $this->query = $query;
        return $this;
    }
}
