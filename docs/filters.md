---
layout: default
title: Use Filters to Predefine Where Conditions
permalink: /filters.html
---
## Use Filters to Predefine Where Conditions

Filters are classes implementing the `ORM\EntityFetcher\FilterInterface` and are applied to an `ORM\EntityFetcher`. For
convenience there is also a `CallableFilter` that allows you to use any callable als filter.

Filters are applied at the very end before the query is built. When the query got executed no further filter will be
applied and filters are not applied to custom queries.

Even when we don't recommend it you can modify the whole query (adding group and order by statements etc.). Have a look
at [QueryBuilder](querybuilder.md) to read more about building queries.

### Basic Filtering

Assuming we often want to filter a specific column `published` to be before now to allow future publications:

```php
use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;

class FilterPublished implements FilterInterface
{
    public function apply(EntityFetcher $fetcher)
    {
        $fetcher->where('published', '<=', date('c'));
    }
}
```

We can then apply this filter to every `EntityFetcher` like that:

```php
/** @var ORM\EntityManager $entityManager */
$fetcher = $entityManager->fetch(Article::class);
$fetcher->filter(new FilterPublished);
```

For an example of a more complex filter have a look at the
[code examples](https://github.com/tflori/orm/blob/master/examples) in the source.

### Global Filtering

We can now also register that filter for every `EntityFetcher` that will be created for the `Article` class with
`EntityFetcher::registerGlobalFilter($class, $filter)` or `Entity::registerGlobalFilter($filter)`. Example:

```php
Article::registerGlobalFilter(new FilterPublished);
```

If we now want to exclude this filter for a specific query we can exclude it with
`$fetcher->withoutFilter($filterClass)`. For Example:

```php
/** @var ORM\EntityManager $entityManager */
$fetcher = $entityManager->fetch(Article::class);
$fetcher->withoutFilter(FilterPublished::class);
```

### Filter Relations

It is possible to add filters to relations. For example to add a relation that directly filters published articles:

```php
use ORM\Entity;

class Article extends Entity {
    protected static $relations = [
        'author' => [User::class, ['authorId' => 'id']],
    ];
}

class User extends Entity {
    protected static $relations = [
        'articles' => [Article::class, 'author'],
        'publishedArticles' => [Article::class, 'author', [FilterPublished::class]],
    ];
}
```

To pass closures or instances of filters you have to create a relation method or create them in the boot method. See
[Relation Definition](relationDefinition.md). Example:

```php
use ORM\Entity;
use ORM\EntityFetcher;
use ORM\Relation\OneToMany;

class User extends Entity {
    protected static $relations = [
        'articles' => [Article::class, 'author'],
    ];
    
    protected static function recentArticlesRelation()
    {
        return new OneToMany(
            'recentArticles', 
            Article::class, 
            'author', 
            [new FilterPublished(), function (EntityFetcher $query) {
                $query->where('createdAt', '>=', date('c', strtotime('-2 weeks')));
            }]
        );
    }
}
```
