<?php

namespace ORM\Entity;

trait EventHandlers
{
    /**
     * Empty event handler
     *
     * Get called when the entity get initialized.
     *
     * @param bool $new Whether or not the entity is new or from database
     * @codeCoverageIgnore dummy event handler
     */
    public function onInit($new)
    {
    }

    /**
     * Empty event handler
     *
     * Get called when something is changed with magic setter.
     *
     * @param string $attribute The variable that got changed.merge(node.inheritedProperties)
     * @param mixed  $oldValue  The old value of the variable
     * @param mixed  $value     The new value of the variable
     * @codeCoverageIgnore dummy event handler
     */
    public function onChange($attribute, $oldValue, $value)
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get inserted in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function prePersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called before the entity get updated in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function preUpdate()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got inserted in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function postPersist()
    {
    }

    /**
     * Empty event handler
     *
     * Get called after the entity got updated in database.
     *
     * @codeCoverageIgnore dummy event handler
     */
    public function postUpdate()
    {
    }
}
