---
layout: default
title: Relation Definition
permalink: /relationDefinition.html
---
## Relation Definition

One of the most important feature of a relational database are relations. A relation is the reference from one row of
a table to another row from the same table or a different table. In our context it means from one object of a subclass
from `Entity` to another object of the same class or another subclass from `Entity`.

Relations get defined in protected static assoc array `$relations`. Each relation gets a name as key and needs at least
two options: the `'class'` is always required and either the `'opponent'` or the `'relation'` (depends if it is the
owner). The default `'cardinality'` is `one` and you can omit this value. 

To make it easier you can write the options in specific order without keys. Please use the order from examples.

Three examples with the same relations:

```php
<?php //?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [User::class, ['userId' => 'id']],
    'comments' => ['many', ArticleComments::class, 'article']
  ];
}
```

```php
<?php //?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        'class' => User::class,
        'relation' => ['userId' => 'id'],
    ],
    'comments' => [
        'cardinality' => 'many',
        'class' => ArticleComments::class,
        'opponent' => 'article',
    ]
  ];
}
```

```php
<?php //?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        self::OPT_RELATION_CLASS => User::class,
        self::OPT_RELATION_RELATION => ['userId' => 'id']
    ],
    'comments' => [
        self::OPT_RELATION_CARDINALITY => 'many',
        self::OPT_RELATION_CLASS => ArticleComments::class,
        self::OPT_RELATION_OPPONENT => 'article',
    ]
  ];
}
```

> We prefer the first one but the third one has auto completion.

| Option                     | Key             | Type     | Description                                      |
|----------------------------|-----------------|----------|--------------------------------------------------|
| `OPT_RELATION_CLASS`       | `'class'`       | `string` | The full qualified name of related class         |
| `OPT_RELATION_RELATION`    | `'relation'`    | `array`  | The column definition (column or property name)  |
| `OPT_RELATION_CARDINALITY` | `'cardinality'` | `string` | How many related objects (one or many) can exist |
| `OPT_RELATION_OPPONENT`    | `'opponent'`    | `string` | The name of the relation in related class        |
| `OPT_RELATION_TABLE`       | `'table'`       | `string` | The table name for many to many relations        |

### Relation Types

Well known there are three types of relationships between entities (*one-to-one*, *one-to-many* and *many-to-many*).
Here we want to describe what is required to define them and how you can define the *non-owner*. 

#### One-To-One

The *one-to-one* relationship is very rarely. Mostly it is used to hide additional data from a otherwise big table. It 
is configured for the owner (the table with the foreign key) exactly the same as a *one-to-many* relationship. The
related entity may have the relation defined.

To define the **owner** the **required** attributes are `'class'` and `'relation'`. To define the **non-owner** the
required attributes are `'class'` and `'opponent'`.

Example (one article has additional data):

```php
<?php //?start_inline=true
class Article extends ORM\Entity {
    protected static $relations = [
        'additionalData' => [ArticleAdditionalData::class, 'article']
    ];
}

// owner with foreign key: articleId
class ArticleAdditionalData extends ORM\Entity {
    protected static $relations = [
        'article' => [Article::class, ['articleId' => 'id']]
    ];
}

$article = $em->fetch(Article::class, 1);
$additionalData = $article->fetch($em, 'additionalData');

echo get_class($additionalData), "\n";                               // ArticleAdditionalData
echo $article === $additionalData->article ? 'true' : 'false', "\n"; // true
```
#### One-To-Many

This is the most used relationship. You can find it in almost every application. Some Examples are "one customer has 
many orders", "one user wrote many articles", "one developer created many repositories" and so on. The owner should
have a relation with cardinality one and the related entity may have a relation with cardinality many.

To define the **owner** the **required** attributes are `'class'` and `'relation'`. To define the **non-owner** the
required attributes are `'cardinality'`, `'class'` and `'opponent'`.

Lets see a complete example (one article has many comments):

```php
<?php //?start_inline=true
class Article extends ORM\Entity {
    protected static $relations = [
        'comments' => ['many', ArticleComments::class, 'article']
    ];
}

// owner with foreign key: articleId
class ArticleComments extends ORM\Entity {
    protected static $relations = [
        'article' => [Article::class, ['articleId' => 'id']]
    ];
}

$article = $em->fetch(Article::class, 1);
$comment = $article->fetch($em, 'comments')->one();

echo get_class($comment), "\n";                               // ArticleComment
echo $article === $comment->article ? 'true' : 'false', "\n"; // true
```

#### Many-To-Many

In tables a *many-to-many* relationship gets solved by two *one-to-many* relationships. For our example we use the 
relationship between articles and categories. One article has many categories and one category has many articles - so
it is indeed a *many-to-many* relationship. You create another table `ArticleCategory` to solve this: one article has
many *article-categories* and one *article-category* has one article and one category and one category has many 
*article-categories*.

```
+---------+          +-----------------+          +----------+
| Article | 1------n | ArticleCategory | n------1 | Category |
+---------+          +-----------------+          +----------+
| id      |          | articleId       |          | id       |
| title   |          | categoryId      |          | name     |
+---------+          +-----------------+          +----------+
```

> If you need additional properties in the relation table you need an entity for it.

As we saw in the other examples: the owner of the relation is the entity that has the foreign key. In a *many-to-many*
relationship there exist no owner and both entities have to define the relationship.

To define the relationship both entities **require** the relation with the attributes `'class'`, `'relation'`, 
`'opponent'` and `'table'`.

Here comes again an example:

```php
<?php //?start_inline=true
class Article extends ORM\Entity {
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'articleId'], 'articles', 'article_category']
    ];
}

class Category extends ORM\Entity {
    protected static $relations = [
        'articles' => [Article::class, ['id' => 'categoryId'], 'categories', 'article_category']
    ];
}

$article = $em->fetch(Article::class, 1);

$category = $article->fetch($em, 'categories')->one();
echo get_class($category), "\n"; // Category

$articlesInCategory = $category->fetch($em, 'articles')->all();
```
