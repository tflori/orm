<?php

namespace ORM\Relation;

use ORM\Exception\InvalidConfiguration;
use ORM\Helper;

trait HasOpponent
{
    /** The name of the relation in the related class
     * @var string */
    protected $opponent;

    /**
     * @param string $requiredType
     * @return Owner|ManyToMany
     * @throws InvalidConfiguration
     */
    protected function getOpponent($requiredType = null)
    {
        $opponent = call_user_func([ $this->class, 'getRelation' ], $this->opponent);

        if ($requiredType && !$opponent instanceof $requiredType) {
            throw new InvalidConfiguration(sprintf(
                "The opponent of a %s relation has to be a %s relation. Relation of type %s returned for relation %s" .
                " of entity %s",
                Helper::shortName(get_class($this)),
                Helper::shortName($requiredType),
                get_class($opponent),
                $this->name,
                $this->parent
            ));
        }

        return $opponent;
    }
}
