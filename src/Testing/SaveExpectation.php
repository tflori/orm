<?php

namespace ORM\Testing;

use Mockery\Exception;
use Mockery\Expectation;
use Mockery\Matcher\IsEqual;
use Mockery\Matcher\MatcherInterface;
use ORM\Entity;
use ORM\Helper;

/**
 * Expectation for save() of a mocked entity that can verify the changes that get written
 */
class SaveExpectation extends Expectation
{
    /** @var bool */
    protected $verifyChanges = false;

    /** @var mixed */
    protected $expectedChanges;

    /**
     * Expect save() to write these changes
     *
     * Pass an array of attribute => new value to expect exactly these changes, or a Mockery matcher (e.g.
     * `m::subset(['title' => 'New'])`) that gets the changes as array of attribute => new value.
     *
     * @param array|MatcherInterface $changes
     * @return $this
     */
    public function withChanges($changes)
    {
        $this->verifyChanges = true;
        $this->expectedChanges = $changes;
        return $this;
    }

    /**
     * Verify the changes of $entity against the expected changes
     *
     * @param Entity $entity
     * @throws Exception
     * @internal
     */
    public function verifyChanges(Entity $entity)
    {
        if (!$this->verifyChanges) {
            return;
        }

        $changes = [];
        foreach ($entity->getDirtyColumns() as $column => $value) {
            $changes[$entity::getAttributeName($column)] = $value;
        }

        $matcher = $this->expectedChanges;
        if (!$matcher instanceof MatcherInterface) {
            $expected = [];
            foreach ($this->expectedChanges as $attribute => $value) {
                $expected[$entity::getAttributeName($entity::getColumnName($attribute))] = $value;
            }
            $matcher = new IsEqual($expected);
            $description = var_export($expected, true);
        } else {
            $description = (string)$matcher;
        }

        if (!$matcher->match($changes)) {
            throw new Exception(sprintf(
                'save() of %s was expected to write %s but writes %s',
                Helper::entityClass($entity),
                $description,
                var_export($changes, true)
            ));
        }
    }
}
