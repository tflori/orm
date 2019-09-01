<?php

namespace ORM\Testing;

use ORM\Entity;
use ORM\EntityManager;
use ORM\Exception\NoEntity;

class EntityManagerMock extends EntityManager
{
    public function fetch($class, $primaryKey = null)
    {
        $reflection = new \ReflectionClass($class);
        if (!$reflection->isSubclassOf(Entity::class)) {
            throw new NoEntity($class . ' is not a subclass of Entity');
        }
        /** @var string|Entity $class */

        if ($primaryKey === null) {
            return new EntityFetcherMock($this, $class);
        }

        $primaryKey = $this->buildPrimaryKey($class, (array)$primaryKey);
        $checksum = $this->buildChecksum($primaryKey);

        if (isset($this->map[$class][$checksum])) {
            return $this->map[$class][$checksum];
        }

        return EntityFetcherMock::retrieve($class, $primaryKey);
    }
}
