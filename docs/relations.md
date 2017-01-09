---
layout: default
title: Working With Relations
permalink: /relations.html
---
## Working With Relations

Relations can be used for joins in EntityFetcher and of course to fetch related objects from Entity. In this doc we
want to describe how and what else you can do with these relations. How to add relations and how to remove relations.

All examples refer to this definitions:

```php
<?php

class Article extends ORM\Entity {
    protected static $relations = [
        'additionalData' => [ArticleAdditionalData::class, ['id' => 'articleId']],
        'comments' => ['many', ArticleComments::class, ['id' => 'articleId'], 'article'],
        'categories' => ['many', Category::class, ['id' => 'articleId'], 'articles', 'article_category'],
        'writer' => [User::class, ['userId' => 'id']]
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
        'articles' => ['many', Article::class, ['id' => 'categoryId'], 'categories', 'article_category']
    ];
}

class User extends ORM\Entity {}

$em = new \ORM\EntityManager();
```

### Fetch relations

You can fetch relations with fetch but also with the default getter. For a relation with cardinality one you will 
always receive the Entity (or null). But for relations with cardinality with many you will receive an array from getter
and a `EntityFetcher` from fetch.

The getter will only execute a query when it is not fetched previously - and for the owner try the mapping first. Fetch
will always execute a query to receive the current data.

For many to many relations the getter try to use mapping so it might be faster.

The getter can be called by magic getter with the name of the relation or by `getRelated()`. The magic getter might not
work when there is a column with the same name.

```php
<?php

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

You can update relations with `setRelated($relation, $entities)`. For the owner in relations this will just call 
`__set($key, $entity->__get($value))` (key and value are from the relation definition). For non owner in one to one and
one to many relations it calls `setRelated($opponent, $this)` to each entity.

The methods do not store the data (neither they don't care about whether the related class is persisted or not). To
store the data you have to call save on each entity. The other problem you might encounter is: it does not update non
related entities - you will have to call `$articleComment->setRelated('article', null)` to remove the writer.

**Many to many is differnt**: for many to many relations there is no owner. You can not just set the related entity. So
there are two other methods: `addRelations($relation, $entities)` and `deleteRelations($relation, $entities)`.

```php
<?php

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
    $article->addRelations('categories', [$em->fetch(Category::class)->where('key', 'php')]);
    $article->save();
    
    $additional = new ArticleAdditionalData();
    $additional->text = 'Lorem ipsum dolor sit amet...';
    $additional->setRelated('article', $article);
    $additional->save();
    
    $em->getConnection()->commit();
}
```
