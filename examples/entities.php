<?php

/**
 * Class User
 *
 * The following annotations are optional
 * @property int id
 * @property string username
 * @property string password
 */
class User extends ORM\Entity {
    protected static $excludedAttributes = ['password'];
}
