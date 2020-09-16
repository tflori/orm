<?php

namespace ORM\Event;

use ORM\Entity;
use ORM\Event;

/**
 * @property-read string $attribute
 * @property-read mixed $oldValue
 * @property-read mixed $newValue
 */
class Changed extends Event
{
    const NAME = 'changed';

    /** @var string */
    protected $attribute;

    /** @var mixed */
    protected $oldValue;

    /** @var mixed */
    protected $newValue;

    public function __construct(Entity $entity, $attribute, $oldValue, $newValue)
    {
        parent::__construct($entity);

        $this->attribute = $attribute;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;
    }
}
