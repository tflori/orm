---
layout: default
title: Relation Definition
permalink: /relationDefinition.html
---

## Relation Definition

> **Upgrading from 1.10 or older:** Relations assigned to `static::$relations` in a boot method are now stored for the
> booted class only. Before, they were written into the `$relations` property that is shared by all entities not
> declaring it themselves, so the relation also showed up in other entities
> ([#91](https://github.com/tflori/orm/issues/91)). This still works, but we recommend `defineRelation()` in the boot
> method or the static property `$relations`.
>
> A boot method has to call `parent::boot()` - otherwise the traits and the boot methods of parent classes are not
> booted. Since 1.11 a warning is triggered when it is missing. See the [Upgrade Guide](upgrade.html) for all changes.

### Cheat Sheet

**TL;DR** Here is a cheat sheet. All information is described below in detail.

<div class="page-break"></div>

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
$comment = $article->fetch('comments')->one();
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
$additionalData = $article->fetch('additionalData');
```

#### Many-To-Many Relation

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'categories' => [Category::class, ['id' => 'article_id'],
            'articles', 'article_category'],
    ];
}

class Category extends ORM\Entity {
    protected static $relations = [
        'articles' => [Article::class, ['id' => 'category_id'],
            'categories', 'article_category'],
    ];
}

$article = $em->fetch(Article::class, 1);
$category = $article->fetch('categories')->one();
```

<div class="page-break"></div>

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
$answers = $comments[0]->fetch('answers')->all();
```

#### Parent-Children Relation

```php
class Category extends ORM\Entity {
    protected static $relations = [
        'parent'   => [self::class, ['parentId' => 'id']],
        'children' => [self::class, 'parent'],
    ];
}

$categories = $em->fetch(Category::class)->all();
// root categories with their children
$tree = Category::getRelation('children')->buildTree(...$categories);
```

#### Working with Relations

```php
// read - the magic getter is getRelated() and fetches only once
$user     = $article->user;
$comments = $article->comments;                // array
$comments = $article->fetch('comments')        // EntityFetcher for many
    ->where('approved', true)->all();

// owner only - save() afterwards
$article->setRelated('user', $user);           // sets userId
$article->setRelated('user', null);            // unlink (also morphed)

// many-to-many only - executes the statement
$article->addRelated('categories', [$category]);
$article->deleteRelated('categories', [$category]);

// eager load
$articles = $em->fetch(Article::class)->with('user', 'comments')->all();
$article->load('comments');                    // a single entity
$em->eagerLoad('comments', ...$articles);      // entities you already have
$article->resetRelated('comments');            // forget loaded (no db change)
```

<div class="page-break"></div>

#### Defining Relations

```php
class Article extends ORM\Entity {
    // 1. static property
    protected static $relations = [
        'user' => [User::class, ['userId' => 'id']],
    ];

    // 2. static method <name>Relation()
    protected static function commentsRelation() {
        return new ORM\Relation\OneToMany(Comment::class, 'article');
    }

    // 3. boot method (since 1.11 - don't assign to static::$relations here)
    protected static function boot() {
        parent::boot();
        static::defineRelation('categories', [
            Category::class, 
            ['id' => 'article_id'], 
            'articles', 
            'article_category'
        ]);
    }
}
```

<div class="page-break"></div>

### Introduction

One of the most important features of relational databases are references between tables. They are also called
relationships or associations - we just say relation. A relation is the reference from one row of a table to another
row according to foreign key. In our object context it means that an entity references to another entity. In both
contexts it can also be the same table or entity in a parent-child relationship.

We define relations between entities in the static property `$relations` (or one of the
[alternatives below](#defining-relations-in-methods-or-the-boot-method)). Each relation gets a name as key and an array
that defines the relationship. It always has to define the related class. The **owner** also needs to define which
columns refer to the **non-owner**. The **non-owner** does not have to define the relationship, but when it
defines the relationship it needs to define at least the name of the relation in the **owner**.

To make it easier you can write the options in specific order without keys. In this case the order is important - so
you have to stick to the order we show in the following examples.

Here are three examples with the same relations:

```php
class Article extends ORM\Entity {
  protected static $relations = [
    'user'     => [User::class, ['userId' => 'id']],   // owner
    'comments' => [ArticleComments::class, 'article'], // non-owner
  ];
}
```

```php
class Article extends ORM\Entity {
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

class Article extends ORM\Entity {
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

| Option        | Const              | Type                | Description                                                                                  |
|---------------|--------------------|---------------------|----------------------------------------------------------------------------------------------|
| `class`       | `OPT_CLASS`        | `string`            | The full qualified name of related class                                                     |
| `reference`   | `OPT_REFERENCE`    | `array`             | The column definition (column or property name)                                              |
| `cardinality` | `OPT_CARDINALITY`  | `string`            | How many related objects (one or many) can exist                                             |
| `opponent`    | `OPT_OPPONENT`     | `string`            | The name of the relation in related class                                                    |
| `table`       | `OPT_TABLE`        | `string`            | The table name for many to many relations                                                    |
| `filters`     | `OPT_FILTERS`      | `array`             | Filter classes to apply to entity fetchers                                                   |
| `morphColumn` | `OPT_MORPH_COLUMN` | `string`            | The column that defines referenced type                                                      |
| `morph`       | `OPT_MORPH`        | `array` or `string` | A map of values to entity classes or<br>a super class (or interface) of all possible classes |

#### Defining Relations in Methods or the Boot Method

Since version 1.9 you can also define relations in a separate method named `<relation>Relation`. The method has to be
static and should return a Relation object. Example:

```php
use ORM\Relation\OneToMany;
use ORM\Relation\Owner;

class Article extends ORM\Entity {
  protected static function userRelation() {
    return new Owner(User::class, ['userId' => 'id']);
  }
  
  protected static function commentsRelation() {
    return new OneToMany(ArticleComments::class, 'article');
  }
}
```

> These methods could also return an array with the relation definition like above, but we don't recommend that.

You can also define relations in the boot method with `defineRelation()` (since version 1.11):

```php
use ORM\Entity;
use ORM\Relation;
class Article extends Entity {
    protected static function boot() {
        parent::boot();
        static::defineRelation('user', new Relation\Owner(User::class, ['userId' => 'id']));
        static::defineRelation('comments', [ArticleComments::class, 'article']);
    }
}
```

> Don't assign relations to `static::$relations` in the boot method (see the note at the top): it looks like an entry
> of the property but acts like a definition in the boot method - a subclass that inherits this boot method and
> declares `$relations` itself gets its own definition overwritten.

All styles work the same way - e.g. the magic getter (`$article->user`) works for every relation regardless of where it
is defined. We recommend the static property `$relations` for most cases, `<relation>Relation()` methods when a
definition depends on something else (e.g. filters depending on the configuration) and `defineRelation()` when the
relations themselves are created at runtime (e.g. by a trait that adds relations to every entity using it).

When a relation with the same name is defined in several ways, the definition from a boot method (also from the boot
method of a parent class through `parent::boot()`) wins over `$relations`, which wins over the `<relation>Relation()`
method.

### Relations in Subclasses

A subclass inherits the relation definitions of its parent class:

- from `$relations` - but when the subclass declares `$relations` itself, it **replaces** the inherited array (that's
  how static properties work in PHP), so you have to repeat the relations you want to keep,
- from `<relation>Relation()` methods,
- from the boot method of the parent class - through `parent::boot()`, which every boot method has to call (a
  subclass without its own boot method inherits it anyway).

Use one style per class hierarchy. To overwrite an inherited relation use the same style as the parent class (or
`defineRelation()` in the boot method of the subclass). Because of the order above, a style with a lower precedence in
the subclass does not overwrite the parent: e.g. a `<relation>Relation()` method in the subclass is ignored when the
parent defines the relation in `$relations`, and `$relations` in the subclass is ignored when the parent defines it in
its boot method.

Every class gets its own relation object: `SpecialArticle::getRelation('tags')` belongs to `SpecialArticle` (which
might live in a different table) and not to `Article`. Keep this in mind for [morphed relations](#morphed-relation-1)
with a map of types: a subclass has to be in the map to be used as parent.

```php
class Article extends ORM\Entity {
    protected static $relations = [
        'tags' => [Tag::class, 'parent'],
    ];
}

class SpecialArticle extends Article {
}

class Tag extends ORM\Entity {
    protected static $relations = [
        'parent' => [['parentType' => [
            'article' => Article::class,
            'special' => SpecialArticle::class, // required to fetch the tags of a SpecialArticle
        ]], ['parentId' => 'id']],
    ];
}
```

### Relation Types

Well known there are three types of relationships between entities: *one-to-one*, *one-to-many* and *many-to-many*.
Here we want to describe what is required to define them and how you can define the **non-owner**.

One important thing is that we need to know who is the owner. We define the **owner** with `'reference'` and the
**non-owner** with `'opponent'` - do not define an **owner** with `'opponent'` otherwise you will get unexpected
behaviour.

The cardinality is determined automatically in most cases. In the short form, that we use in the examples, you omit
the cardinality - the only exception is `'one'` for the **non-owner** of a *one-to-one* relation.

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
$comment = $article->fetch('comments')->one();

echo get_class($comment), "\n";                               // ArticleComments
echo $article === $comment->article ? 'true' : 'false', "\n"; // true
```

##### The Parent-Children Relation

There is a special one-to-many relation that is called the parent-children relation. The relation is defined
automatically when the entity that defines the relation is the entity referenced in the relation. The only additional
feature this relation has at the moment is that you can fill up the children without particularly eager load them.

For example, you could load all items and then build the tree from them:

```php
class Album extends ORM\Entity {
    protected static $relations = [
        'parent' => [self::class, ['parentId' => 'id']],
        'children' => [self::class, 'parent'],
    ];
}

$albums = Album::query()->orderBy('name')->all();
$tree = Album::getRelation('children')->buildTree(...$albums);
```

> We might implement features like recursive queries to load all parents or all children. In the meantime we suggest to
> use [materialized paths](https://dzone.com/articles/materialized-paths-tree-structures-relational-database) or other
> strategies to find your parents or children.

Example, load a subtree using materialized path:

```php
$albums = Album::query()->where('path', 'LIKE', '3.5%')->all(); // album 3.5 and all its children
$treeOf35 = Album::getRelation('children')->buildTree(...$albums);
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
$additionalData = $article->fetch('additionalData');

echo get_class($additionalData), "\n";                               // ArticleAdditionalData
echo $article === $additionalData->article ? 'true' : 'false', "\n"; // true
```

> When you omit the cardinality it is a *one-to-many* relationship and you will get an `EntityFetcher` from fetch.

#### Many-To-Many

A *many-to-many* relationship gets solved by two *one-to-many* relationships in an association table. For our example
we use the relationship between articles and categories. One article has many categories and one category has many
articles. You create another table `article_category` to solve the relationship:

* one article has many *article-categories*
* one *article-category* has one article and one category
* one category has many *article-categories*

```
+---------+          +------------------+          +----------+
| Article | 1------n | article_category | n------1 | Category |
+---------+          +------------------+          +----------+
| id      |          | article_id       |          | id       |
| title   |          | category_id      |          | name     |
+---------+          +------------------+          +----------+
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

$category = $article->fetch('categories')->one();
echo get_class($category), "\n"; // Category

$articlesInCategory = $category->fetch('articles')->all();
```

#### Morphed Relation

A morphed relation defines additionally a type on the owner side to reference different entities. The advantage is that
you don't need to create several tables that store the same data. On the other hand this approach comes with some
drawbacks (see below).

To define morphed relations you have to pass the morph definition (attribute `'morph'`) and the morph column (attribute
`'morphColumn'`) instead of a class. The morph column is obviously just a string with the attribute or column name.

Imagine a theater ticket system where you can buy tickets for the whole season, an event with multiple plays or a
single play:

```
+-------------+              +-------------+
| Ticket      | n-----+----1 | Season      |
+-------------+       |      +-------------+
| id          |       |      | id          |
| entityType  |       |      | year        |
| entityId    |       |      +-------------+
+-------------+       |
                      |      +-------------+
                      +----1 | Event       |
                      |      +-------------|
                      |      | id          |
                      |      | name        |
                      |      +-------------+
                      |
                      |      +-------------+
                      +----1 | Play        |
                             +-------------+
                             | id          |
                             | name        |
                             +-------------+
```

##### Morph Definition

The morph definition can be either a class name of a super class that the referenced classes have to extend, an
interface name that the referenced classes have to implement, or an array that maps the values in the morph column to
entity classes.

###### Superclass or interface

When you define a class name as the morph the related objects have to extend this class. Or they have to implement the
interface when you define the interface. As this definition is required you have to pass at least the class `ORM\Entity`
to create a valid definition.

> Internally this gets checked with `$entity instanceof $this->super`

```php
$relation['morph'] = ORM\Entity::class;
```

###### Morph map

Instead of having the application specific class name in the database you might want to have an abstract name of the
referenced entity in the morph column. This is possible by defining a map that defines which value stands for which
class.

When you define a morph map, the related objects have to be one of the defined classes in this map. The array keys of
this map are the values that appear in the column, and the value have to be a class name. The classes are compared
exactly - subclasses are not included automatically (see [Relations in Subclasses](#relations-in-subclasses)). With a
superclass or interface instead of a map, subclasses are allowed and their class name is stored in the morph column.

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
$relation['reference'] = ['entityId'];
```

> To get the primary key of the same entity one million times takes about 0.2 seconds in php 5.6 and 0.07 seconds
> in php 7.4 on a 4ghz cpu.

###### Map of foreign keys to primary keys

When the primary key is always the same (for example id) then it is a bit faster (by a small fragment of a second) to
define the key used, like we do in other references.

```php
$relation['reference'] = ['entityId' => 'id'];
```

###### Reference definition per type

It is also possible to define completely different references per type by providing the type as key and the reference
as array, like for other references.

```php
$relation['reference'] = [
    'season' => ['entityId' => 'id'],
    'event' => ['entityId' => 'id'],
    'play' => ['entityId' => 'id'],
];
```

> In this example that doesn't make sense but see drawbacks #1 for an example where it makes sense.

If you are not using a morph map use class names as keys. Please **note** that this implicitly generates a morph map
that restricts the usage of other classes.

```php
$relation['reference'] = [
    Season::class => ['entityId' => 'id'],
    Event::class => ['entityId' => 'id'],
    Play::class => ['entityId' => 'id'],
];
```

###### Primary key definition per type

To define different primary keys per type use an array for the primary keys where the key is the type, and the value is
the primary key.

```php
$relation['reference'] = [
    'entityId' => [
        'season' => 'id',
        'event' => 'id',
        'play' => 'id',
    ],
];
```

> This only makes sense if the referenced key is different for each type.

Again: if you are not using a morph map use class names as keys (this implicitly generates a morph map).

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
type (here with a column per type - `seasonId`, `eventId` and `playId` - instead of `entityId`).

```php
class Ticket extends ORM\Entity {
    protected static $relations = [
        'event' => [['entityType' => [
            'season' => Season::class, 
            'event' => Event::class,
            'play' => Play::class,
         ]], [
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
         JOIN (SELECT 'season' AS type, id, "common_column_1", "common_column_2"
               FROM seasons
               UNION
               SELECT 'event' AS type, id, "common_column_1", "common_column_2"
               FROM events
               UNION
               SELECT 'play' AS type, id, "common_column_1", "common_column_2"
               FROM plays) stmt ON stmt.type = entityType AND stmt.id = entityId
```

> You can write this query manually or extend the morphed relation if you want. If you find a good solution we would
> also appreciate a pull request.

###### 3. There is no many-to-many relationship definition for morphed relations

At the moment this is simply not implemented. You can solve it with a pivot entity - use eager loading
(`->with('ticketEvents.event')`) to avoid a query per entity:

```php
class TicketEvent extends ORM\Entity {
    protected static $relations = [
        'ticket' => [Ticket::class, ['ticketId' => 'id']],
        'event' => [['entityType' => [
            'season' => Season::class, 
            'event' => Event::class,
            'play' => Play::class,
         ]], ['entityId']],
    ];
}

class Ticket extends ORM\Entity {
    protected static $relations = [
        'ticketEvents' => [TicketEvent::class, 'ticket'],
    ];
}

// loads the tickets, all their ticket events and then the events with one query per type
$tickets = Ticket::query()->with('ticketEvents.event')->all();
foreach ($tickets[0]->ticketEvents as $ticketEvent) {
    echo get_class($ticketEvent->event); // Season, Event or Play
}
```

> It would be possible to develop such a relation, but it has some caveats, and these morphed relations already add a
> shitload of complexity. Again: please come up with a pull request we would highly appreciate it.
