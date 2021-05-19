---
layout: default
title: RelationExample Definition
permalink: /relationDefinition.html
---
## Relation Definition

### Cheat Sheet

**TL;DR** Here is a cheat sheet. All information is described below in detail.

#### One-To-Many Relation

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'comments' => [ArticleComments::class, 'article']
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

#### One-To-One Relation

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'additionalData' => ['one', ArticleAdditionalData::class, 'article']
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

#### Many-To-Many Relation

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category']
    ];
}

class Category extends ORM\Entity {
    protected static $relations = [
        'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category']
    ];
}

$article = $em->fetch(Article::class, 1);

$category = $article->fetch($em, 'categories')->one();
echo get_class($category), "\n"; // Category

$articlesInCategory = $category->fetch($em, 'articles')->all();
```

#### Morphed Relation

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'comments' => [Comment::class, 'parent'],
    ];
}

class Image extends ORM\Entity {
    protected static $relations = [
        'comments' => [Comment::class, 'parent'],
    ];
}

class Comment extends ORM\Entity {
    protected static $relations = [
        'answers' => [Comment::class, 'parent'],
        'parent' => [['parentType' => [
            'comment' => Comment::class,
            'article' => Article::class,
            'image'   => Image::class,
        ]], ['parentId' => 'id']],
    ];
}

$article = $em->fetch(Article::class, 1);

$comments = $article->fetch('comments')->all();
$article = $comments[0]->parent;
$answers = $comments[0]->fetch('answers')->all();
$comment = $answers[0]->parent;
```

### Introduction

One of the most important features of relational databases are references between tables. They are also called
relationships or associations - we just say relation. A relation is the reference from one row of a table to another
row according to foreign key. In our object context it means that an entity references to another entity. In both
contexts it can also be the same table or entity in a parent-child relationship.

We define relations between entities in the static property `$relations`. Each relation gets a name as key and an array
that defines the relationship. It always has to define the related class. The **owner** also needs to define which 
columns refer to the **non-owner**. The **non-owner** does not have to define the relationship, but when it
defines the relationship it needs to define at least the name of the relation in the **owner**.

To make it easier you can write the options in specific order without keys. In this case the order is important - so
you have to stick to the order we show in the following examples.

Here are three examples with the same relations:

```php
class Article extends ORM\ENtity {
  protected static $relations = [
    'user'     => [User::class, ['userId' => 'id']],   // owner
    'comments' => [ArticleComments::class, 'article'], // non-owner
  ];
}
```

```php
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        'class'     => User::class,
        'reference' => ['userId' => 'id'],
    ],
    'comments' => [
        'cardinality' => 'many', // default
        'class'       => ArticleComments::class,
        'opponent'    => 'article',
    ],
  ];
}
```

```php
use ORM\Relation;

class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        Relation::OPT_CLASS     => User::class,
        Relation::OPT_REFERENCE => ['userId' => 'id'],
    ],
    'comments' => [
        Relation::OPT_CARDINALITY => Relation::CARDINALITY_MANY, // default
        Relation::OPT_CLASS       => ArticleComments::class,
        Relation::OPT_OPPONENT    => 'article',
    ],
  ];
}
```

> We prefer the first one but the third one has auto completion.

These are the options for relation definitions.

| Option        | Const              | Type     | Description                                      |
|---------------|--------------------|----------|--------------------------------------------------|
| `class`       | `OPT_CLASS`        | `string` | The full qualified name of related class         |
| `reference`   | `OPT_REFERENCE`    | `array`  | The column definition (column or property name)  |
| `cardinality` | `OPT_CARDINALITY`  | `string` | How many related objects (one or many) can exist |
| `opponent`    | `OPT_OPPONENT`     | `string` | The name of the relation in related class        |
| `table`       | `OPT_TABLE`        | `string` | The table name for many to many relations        |
| `filters`     | `OPT_FILTERS`      | `array`  | Filter classes to apply to entity fetchers       |
| `morphColumn` | `OPT_MORPH_COLUMN` | `string` | The column that defines referenced type          |
| `morph`       | `OPT_MORPH`        | `array`  | A map of values to entity classes                |
|  - - " - -    |   - - " - -        | `string` | A super class of all the subclasses possible     | 

Since version 1.9 you can also define relations in a separate method named `<relation>Relation`. The method has to be
static and should return a Relation object. Example:

```php
use ORM\Relation\OneToMany;
use ORM\Relation\Owner;

class Article extends ORM\ENtity {
  protected static function userRelation() {
    return new Owner('user', User::class, ['userId' => 'id']);
  }
  
  protected static function commentsRelation() {
    return new OneToMany('comments', ArticleComments::class, 'article');
  }
}
```

> These methods could also return an array with the relation definition like above, but we don't recommend that.

Also since version 1.9 you can use the boot method to create relations:

```php
use ORM\Entity;
use ORM\Relation;
class Article extends Entity {
    protected static function boot() {
        static::$relations['user'] = new Relation\Owner(User::class, ['userId' => 'id']);
        static::$relations['comments'] = new Relation\OneToMany(ArticleComments::class, 'article');
    }
}
```

### RelationExample Types

Well known there are three types of relationships between entities: *one-to-one*, *one-to-many* and *many-to-many*.
Here we want to describe what is required to define them and how you can define the **non-owner**.

One important thing is that we need to know who is the owner. We define the **owner** with `'reference'` and the 
**non-owner** with `'opponent'` - do not define an **owner** with `'opponent'` otherwise you will get unexpected
behaviour.

The cardinality is mostly determined automatically and also overwritten. In the short form, that we use in the examples,
you have to omit the cardinality and there is only one circumstance where a `'one'` is allowed to define.

#### One-To-Many

This is the most used relationship. You can find it in almost every application. Some Examples are "one customer has 
many orders", "one user wrote many articles", "one developer created many repositories" and so on.

To define the **owner** the **required** attributes are `'class'` and `'reference'`. To define the **non-owner** the
**required** attributes are `'class'` and `'opponent'`.

Lets see an example (one article has many comments):

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'comments' => [ArticleComments::class, 'article']
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

#### One-To-One

A *one-to-one* relationship is mostly used to store data, that is not required for every operation, in a separated
table. It is configured exactly the same as a *one-to-many* relationship except for the **non-owner** where the
cardinality can not be determined automatically.

To define the **owner** the **required** attributes are `'class'` and `'reference'`. To define the **non-owner** the
**required** attributes are `'cardinality'`, `'class'` and `'opponent'`.

Example (one article has additional data):

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'additionalData' => ['one', ArticleAdditionalData::class, 'article']
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

> When you omit the cardinality it is a *one-to-many* relationship and you will get an `EntityFetcher` from fetch.

#### Many-To-Many

A *many-to-many* relationship gets solved by two *one-to-many* relationships in an association table. For our example
we use the relationship between articles and categories. One article has many categories and one category has many
articles. You create another table `ArticleCategory` to solve the relationship:

 * one article has many *article-categories* 
 * one *article-category* has one article and one category
 * one category has many *article-categories*

```
+---------+          +-----------------+          +----------+
| Article | 1------n | ArticleCategory | n------1 | Category |
+---------+          +-----------------+          +----------+
| id      |          | articleId       |          | id       |
| title   |          | categoryId      |          | name     |
+---------+          +-----------------+          +----------+
```

> If you need additional properties in the association table you need an entity for it and create indeed two
> *one-to-many* relationships.

As we have seen in the other examples: the owner of the relation is the entity that has the foreign key. In a
*many-to-many* relationship there is no owner and both entities have to define the reference in the association table.
To define the relationship both entities **require** the attributes `'class'`, `'reference'`, `'opponent'` and 
`'table'`.

**ATTENTION**: Because we don't have an entity in the middle the foreign key column in the association table has to be
the column name and not the variable name.

Here comes again an example:

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category']
    ];
}

class Category extends ORM\Entity {
    protected static $relations = [
        'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category']
    ];
}

$article = $em->fetch(Article::class, 1);

$category = $article->fetch($em, 'categories')->one();
echo get_class($category), "\n"; // Category

$articlesInCategory = $category->fetch($em, 'articles')->all();
```

#### Morphed Relation

A morphed relation defines additionally a type on the owner side to reference different entities. The advantage is that
you don't need to create several tables that store the same data. On the other hand this approach comes with some
drawbacks (see below).

1. You can't write a join from the owner to the different entities
2. 
3. There is no many-to-many relationship definition for morphed relations. In that case you also need to create an 
   entity in between.
   
To define morphed relations you have to pass the morph definition (attribute `'morph'`) and the morph column (attribute
`'morphColumn'` instead of a class. The morph column is obviously just a string with the attribute or column name.

Imagine a theater ticket system where you can buy tickets for the whole season, an event with multiple plays or a
single play:

```
+-------------+              +-----------+
| Ticket      | n-----+----1 | Season    +
+-------------+       |      +-----------+
| id          |       |      | id        |
| eventType   |       |      | year      |
| eventId     |       |      +-----------+
+-------------+       |
                      |      +-----------+
                      +----1 | Event     |
                      |      +-----------|
                      |      | id        |
                      |      | name      |
                      |      +-----------+
                      |
                      |      +-----------+
                      +----1 | Play      |
                             +-----------+
                             | id        |
                             | name      |
                             +-----------+
```

##### Morph Definition

The morph definition can be either a class or instance name that the referenced classes have to extend/implement or an
array that maps the values in the morph column to entity classes.

###### Superclass or interface

When you define a class name as the morph the related objects have to extend this class. Or they have to implement the
interface when you define the interface. As this definition is required you have to pass at least the class `ORM\Entity`
to create a valid definition.

> Internally this gets checked with `$entity instanceof $this->super`

```php
$relation['morph'] = ORM\Entity::class;
```

###### Morph Map

Instead of having the application specific class name in the database you might want to have an abstract name of the
referenced entity in the morph column. This is possible by defining a map that defines which value stands for which
class.

When you define a morph map, the related objects have to be one of the defined classes in this map. The array keys of
this map are the values that appear in the column, and the value have to be a class name.

```php
$relation['morph'] = [ 
  'season' => Season::class, 
  'event' => Event::class, 
  'play' => Play::class, 
];
```

##### Reference Definition

The reference for a morphed relation may be different per type - so you can define the reference per type or just with
the foreign key where the primary key will be used from the referenced entity.

###### A bare foreign key

When you define just a single foreign key, the primary key of the referenced object will be used.

```php
$relation['reference'] = ['eventId'];
```

> To get the primary key of the same entity one million times takes about 0.2 seconds in php 5.6 and 0.07 seconds
> in php 7.4 on a 4ghz cpu.

###### Map of foreign keys to primary keys

When the primary key is always the same (for example id) then it is a bit faster (by a small fragment of a second) to
define the key used, like we do in other references.

```php
$relation['reference'] = ['eventId' => 'id'];
```

###### Reference definition per type

It is also possible to define completely different references per type by providing the type as key and the reference
as array, like for other references. 

```php
$relation['reference'] = [
    'season' => ['eventId' => 'id'],
    'event' => ['eventId' => 'id'],
    'play' => ['eventId' => 'id'],
];
```

> In this example that doesn't make sense but see drawbacks #1 for an example where it makes sense.

If you are not using a morph map use class names as keys. Please **note** that this implicit generates a morph map that
restricts the usage of other classes.

```php
$relation['reference'] = [
    Season::class => ['eventId' => 'id'],
    Event::class => ['eventId' => 'id'],
    Play::class => ['eventId' => 'id'],
];
```

###### Primary key definition per type

To define different primary keys per type use an array for the primary keys where the key is the type, and the value is
the primary key.

```php
$relation['reference'] = [
    'eventId' => [
        'season' => 'id',
        'event' => 'id',
        'play' => 'id',
    ],
];
```

> This only makes sense if the referenced key is different for each type.

Again: if you are not using a morph map use class names as keys (this implicit generates a morph map).

##### Short Form

To define it in the short form instead of a class you provide an array with only one item. The key is the morph column,
and the value is the morph definition.

See this example for a comparison ("a" and "b" define the same relation):

```php
use ORM\Relation;
$relations = [];

$relations['a'] = [
    Relation::OPT_MORPH_COLUMN => '<morphColumn>',
    Relation::OPT_MORPH => [
        '<type1>' => '<class1>',
        '<type2>' => '<class2>', 
    ],
    Relation::OPT_REFERENCE => ['<foreignKey>' => '<primaryKey>'],
];

$relations['b'] = [['<morphColumn>' => [
     '<type1>' => '<class1>',
     '<type2>' => '<class2>',
]], ['<foreignKey>' => '<primaryKey>']];
```

##### Drawbacks of Morphed Relations

###### 1. The foreign key can not be defined as foreign key in the database

Because the foreign key cannot reference different tables based on another column (at least not in MySQL, Postgres and 
SQLite) you cannot define them in the database. You could still create triggers to check if a row in table A gets 
deleted. But to be honest that is not a foreign key.

When you [define the reference by type](#reference-definition-per-type) you can use different foreign keys for each
type.

```php
class Ticket extends ORM\Entity {
    protected static $relations = [
        'event' => ['eventType' => [
            'season' => Season::class, 
            'event' => Event::class,
            'play' => Play::class,
         ], [
            'season' => ['seasonId' => 'id'],
            'event' => ['eventId' => 'id'],
            'play' => ['playId' => 'id'],
         ]],
    ];
}
```

**NOTE:** when you define a morph map and references by type you have to provide references for every type in the morph
map.

###### 2. You can't write a join from the owner to the different entities

Because you don't know what you get, you also don't know what columns will exist. But even when you know they all have a 
specific column you would need a sub query where you union all types to one table with a column to match against the
type column. To generate this query could only work for relations with a morph map and a definition which columns are
in common. Example query:

```sql
SELECT * 
FROM tickets
  JOIN (
      SELECT 'season' AS type, id, "common_column_1", "common_column_2" FROM seasons UNION
      SELECT 'event' AS type, id, "common_column_1", "common_column_2" FROM events UNION
      SELECT 'play' AS type, id, "common_column_1", "common_column_2" FROM plays
  ) stmt ON stmt.type = eventType AND stmt.id = eventId
```

> You can write this query manually or extend the morphed relation if you want. If you find a good solution we would
> also appreciate a pull request.

###### 3. There is no many-to-many relationship definition for morphed relations

At the moment this is simply not implemented you can mitigate this issue by using a pivot entity, but it might get
very slow for larger lists:

```php
class TicketEvent extends ORM\Entity {
    protected static $relations = [
        'ticket' => [Ticket::class, ['ticketId' => 'id']],
        'event' => ['eventType' => [
            'season' => Season::class, 
            'event' => Event::class,
            'play' => Play::class,
         ], ['eventId']],
    ];
}

class Ticket extends ORM\Entity {
    protected static $relations = [
        'ticketEvents' => [TicketEvent::class, 'ticket'],
    ];
}

foreach (Ticket::query()->one()->ticketEvents as $ticketEvent) {
    echo get_class($ticketEvent->event); // Season, Event or Play
}
```

> It would be possible to develop such a relation, but it has some caveats, and these morphed relations already add a
> shitload of complexity. Again: please come up with a pull request we would highly appreciate it.
