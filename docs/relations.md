---
layout: default
title: Relations
permalink: /relations.html
---
## Relations

One of the most important feature of a relational database are relations. A relation is the reference from one row of
a table to another row from the same table or a different table. In our context it means from one object of a subclass
from `Entity` to another object of the same class or another subclass from `Entity`.
 
### Defining relations

Relations get defined in protected static assoc array `$relations`. Each relation gets a name as key and has three 
settings: cardinality, class and relation. The default cardinality is `one` and you can omit this value. To make it
easier you can write them in an array without or with keys.

Three examples with the same relations:

```php?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [User::class, ['userId' => 'id']],
    'history' => ['many', ArticleHistory::class, ['id' => 'articleId']]
  ];
}
```

```php?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        'class' => User::class, 
        'relation' => ['userId' => 'id']
    ],
    'history' => [
        'cardinality' => 'many',
        'class' => ArticleHistory::class,
        'relation' => ['id' => 'articleId']
    ]
  ];
}
```

```php?start_inline=true
class Article extends ORM\ENtity {
  protected static $relations = [
    'user' => [
        self::OPT_RELATION_CLASS => User::class, 
        self::OPT_RELATION_RELATION => ['userId' => 'id']
    ],
    'history' => [
        self::OPT_RELATION_CARDINALITY => 'many',
        self::OPT_RELATION_CLASS => ArticleHistory::class,
        self::OPT_RELATION_RELATION => ['id' => 'articleId']
    ]
  ];
}
```

> We prefer the first one but the third one has auto completion
