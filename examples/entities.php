<?php

/**
 * Trait that adds an 'author' relation to any entity with an authorId column.
 * boot<TraitName>() is called automatically once per class on first use.
 *
 * @property int $authorId
 */
trait HasAuthor
{
    public static function bootHasAuthor()
    {
        static::$relations['author'] = new ORM\Relation\Owner(User::class, ['authorId' => 'id']);
    }
}

/**
 * @property int    $id
 * @property string $username
 * @property string $password
 * @property int    $avatarId
 */
class User extends ORM\Entity
{
    protected static $excludedAttributes = ['password'];
    protected static $includedAttributes = ['name'];

    protected static $relations = [
        // Owner: FK avatarId on User, Image defines no opponent
        'avatar'   => [Image::class, ['avatarId' => 'id']],
        // Non-owner opponents (FK lives on the other side)
        'articles' => [Article::class, 'author'],
        'comments' => [Comment::class, 'author'],
    ];

    public function getName()
    {
        return ucwords(preg_replace('/[^A-Za-z0-9\-]+/', ' ', $this->username));
    }
}

/**
 * @property int    $id
 * @property string $name
 * @property int    $parentId
 */
class Category extends ORM\Entity
{
    protected static $relations = [
        // ManyToMany with Article via pivot table article_category
        'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category'],
        // ParentChildren: detected automatically because entity references itself
        'parent'   => [self::class, ['parentId' => 'id']],
        'children' => [self::class, 'parent'],
    ];
}

/**
 * @property int    $id
 * @property int    $authorId
 * @property int    $teaserId
 * @property string $title
 */
class Article extends ORM\Entity
{
    use HasAuthor;

    protected static $relations = [
        // Owner: FK teaserId on Article, Image defines no opponent
        'teaser'     => [Image::class, ['teaserId' => 'id']],
        // ManyToMany via pivot table article_category
        'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category'],
        // Morphed OneToMany: Comment.parent can reference Article or Image
        'comments'   => [Comment::class, 'parent'],
    ];
    // 'author' is added by HasAuthor::bootHasAuthor()
}

/**
 * @property int $id
 * @property int $authorId
 */
class Comment extends ORM\Entity
{
    use HasAuthor;

    protected static $relations = [
        // Morphed Owner: parentType column determines actual class
        'parent' => [['parentType' => [
            'article' => Article::class,
            'image'   => Image::class,
        ]], ['parentId' => 'id']],
    ];
    // 'author' is added by HasAuthor::bootHasAuthor()
}

/**
 * @property int    $id
 * @property string $url
 */
class Image extends ORM\Entity
{
    protected static $relations = [
        // Morphed OneToMany opponent
        'comments' => [Comment::class, 'parent'],
        // No 'articles' or 'users' — teaser/avatar define no opponent
    ];
}