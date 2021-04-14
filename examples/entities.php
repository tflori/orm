<?php

/**
 * Class User
 *
 * The following annotations are optional
 * @property int id
 * @property string username
 * @property string password
 */
class User extends ORM\Entity
{
    protected static $excludedAttributes = ['password'];
    protected static $includedAttributes = ['name'];

    public function getName()
    {
        return ucwords(preg_replace('/[^A-Za-z0-9\-]+/', ' ', $this->username));
    }
}

class Comment extends ORM\Entity
{
    protected static $relations = [
        'parent' => [['parentType' => [
            'article' => Article::class,
            'image' => Image::class,
        ]], ['parentId' => 'id']],
    ];
}

class Article extends ORM\Entity
{
    protected static $relations = [
        'comments' => [Comment::class, 'parent'],
    ];
}

class Image extends ORM\Entity
{
    protected static $relations = [
        'comments' => [Comment::class, 'parent'],
    ];
}
