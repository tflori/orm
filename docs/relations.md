---
layout: default
title: Working With Relations
permalink: /relations.html
---
## Working With Relations

Relations can be used for joins in `EntityFetcher` and of course to fetch related objects from Entity. In this doc we
want to describe how and what else you can do with these relations. How to add relations and how to remove relations.

All examples refer to this definitions:

```php?start_inline=true
class Article extends ORM\Entity {
    protected static $relations = [
        'additionalData' => ['one', ArticleAdditionalData::class, 'article'], // 1:1
        'comments' => [ArticleComments::class, 'article'], // 1:n
        'categories' => [Category::class, ['id' => 'articleId'], 'articles', 'article_category'], // n:m
        'writer' => [User::class, ['userId' => 'id']] // n:1
    ];
}

class ArticleAdditionalData extends ORM\Entity {
    protected static $relations = [
        'article' => [Article::class, ['articleId' => 'id']]
    ];
}

class ArticleComments extends ORM\Entity {
    protected static $relations = [
        'article' => [Article::class, ['articleId' => 'id']]
    ];
}

class Category extends ORM\Entity {
    protected static $relations = [
        'articles' => [Article::class, ['id' => 'categoryId'], 'categories', 'article_category']
    ];
}

class User extends ORM\Entity {}

$em = new \ORM\EntityManager();
```

### Fetch relations

You can fetch relations with `fetch($relation)` and with `getRelated($relation)` (or the magic getter). For a relation
with cardinality *one* you will always receive the Entity (or null). But for relations with cardinality *many* you will
receive an array from getter and an `EntityFetcher` from fetch.

The getter will only execute a query when it is not fetched previously. Fetch will always call `fetch($class)` on the
`EntityManager`. For an owner it can use the primary key and therefore it can use the mapping without executing a 
query.

For *many-to-many* relations the getter will first fetch all primary ids from relation table and then use
`fetch($class, $primaryKey)` from `EntityManager`.  Under some circumstances this can be faster.

The method `getRelated($relation)` can be called by magic getter with the name of the relation as property. This might
not work when there is a column with the same name as the relation.

```php?start_inline=true
/** @var Article $article */
$article = $em->fetch(Article::class, 1);

echo get_class($article->getRelated('writer')), "\n"; // User
echo get_class($article->writer), "\n"; // User
echo get_class($article->fetch('writer')), "\n"; // User

echo gettype($article->getRelated('comments')), "\n"; // array
echo get_class($article->getRelated('comments')[0]), "\n"; // ArticleComment
echo get_class($article->fetch('comments')), "\n"; // ORM\EntityFetcher
```

### Update Relations

You can update relations with `setRelated($relation, $entity)` for owners. For non owner in *one-to-one*
and *one-to-many* relations you have to get the owner and call `setRelated()` on the owner.

For *many-to-many* relations there is no owner - you can not just set the related entity. So there are two other 
methods: `addRelated($relation, $entities)` and `deleteRelated($relation, $entities)`.

```php?start_inline=true
// Example - Create a comment:
/** @var Article $article */
if ($article = $em->fetch(Article::class, 1)) {
    $comment = new ArticleComment();
    $comment->author = 'iras';
    $comment->text = 'Awesome!';
    $comment->setRelated('article', $article);
    $comment->save($em);
}

// Example - create an article
/** @var User $user */
if ($user = @$_SESSION['user']) {
    $em->getConnection()->beginTransaction();
    
    $article = new Article();
    $article->title = 'An amazing title that points out nothing';
    $article->setRelated('writer', $user);
    $article->addRelated('categories', [$em->fetch(Category::class)->where('key', 'php')]);
    $article->save();
    
    $additional = new ArticleAdditionalData();
    $additional->text = 'Lorem ipsum dolor sit amet...';
    $additional->setRelated('article', $article);
    $additional->save();
    
    $em->getConnection()->commit();
}

// Example - update categories
/** @var Article $article */
if ($article = $em->fetch(Article::class, 1)) {
    $currentCategories = $article->getRelated('categories');
    $categoryKeys = $_POST['categories'];
    $newCategories = $em->fetch(Category::class)->where('key', $categoryKeys)->all();
    
    $em->getConnection()->beginTransaction();
    $article->deleteRelated(array_diff($currentCategories, $newCategories));
    $article->addRelated(array_diff($newCategories, $currentCategories));
    $em->getConnection()->commit();
}
```
