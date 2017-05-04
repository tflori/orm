<?php

namespace ORM\Dbal;

use ORM\Dbal;

class Sqlite extends Dbal
{
    public function insert($entity, $useAutoIncrement = true)
    {
        $statement = $this->buildInsertStatement($entity);
        $pdo = $this->em->getConnection();

        if ($useAutoIncrement && $entity::isAutoIncremented()) {
            $pdo->query($statement);
            return $pdo->lastInsertId();
        }

        $pdo->query($statement);
        $this->em->sync($entity, true);
        return true;
    }
}
