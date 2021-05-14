---
layout: default
title: Use Filters to Predefine Where Conditions
permalink: /filters.html
---
## Use Filters to Predefine Where Conditions

Filters are classes implementing the `ORM\EntityFetcher\FilterInterface` and are applied to an `ORM\EntityFetcher`. For
convenience there is also a `CallableFilter` that allows you to use any callable as filter.

Filters are applied at the very end before the query is built. When the query got executed no further filter will be
applied. To custom queries set with `$fetcher->setQuery()` filters are not applied at all.

Even when we don't recommend it you can modify the whole query (adding group and order by statements etc.). Have a look
at [QueryBuilder](querybuilder.md) to read more about building queries.

### Basic Filtering

You can create filters by implementing the `FilterInterface` and then applying it on any `EntityFetcher` with 
`$fetcher->filter($filter)`. This method allows you to pass either a class name of a filter, an instance of a filter,
a callable or a closure.

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

function filterPublished(EntityFetcher $fetcher) {
    $fetcher->where('published', '<=', date('c'));
}

/** @var ORM\EntityManager $entityManager */
$articles = $entityManager->fetch(Article::class)
    ->filter(FilterPublished::class)
    ->all();

// other options
$fetcher = $entityManager->fetch(Article::class);
$fetcher->filter('filterPublished'); // a callable
$fetcher->filter(new FilterPublished()); // an instance
$fetcher->filter(function (EntityFetcher $fetcher) { // a closure
    $fetcher->where('published', '<=', date('c'));
});
```

For an example of a more complex filter have a look at the
[code examples](https://github.com/tflori/orm/blob/master/examples) in the source.

### Global Filtering

Filters can be registered globally. That means that any `EntityFetcher` for a specific class applies that filter. 
Entity fetchers for a subclass of the class applies that filter too. You can therefore register filters for `ORM\Entity`
and they are applied for every `EntityFetcher`.

You can register a filter globally with `EntityFetcher::registerGlobalFilter($class, $filter)` 
or `Entity::registerGlobalFilter($filter)`. Example:

```php
Article::registerGlobalFilter(new FilterPublished);
// or a super global filter
ORM\Entity::registerGlobalFilter(new FilterPublished);
```

Filters can be excluded for a `EntityFetcher` instance with `$fetcher->withoutFilter($class)`. Example:

```php
/** @var ORM\EntityManager $entityManager */
$fetcher = $entityManager->fetch(Article::class);
$fetcher->withoutFilter(FilterPublished::class);
```

> Note: at the moment it is not possible to remove a specific instance of a filter.

### Filter Relations

It is possible to add filters to relations. By adding a filter to a relation this filter is always used when you fetch
the related objects with `$entity->fetch($relation)` or `$entity->getRelated($relation)`. Filters can only be defined
for the non-owner of one-to-many or one-to-one relations and for many-to-many relations (a simple rule: If you have to
define an opponent you can also define filters).

For example to add a relation that directly filters published articles:

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

Naturally (php fact) you can't create instances or closures in property definitions. To pass closures or instances of
filters you have to create a relation method or create them in the boot method. See
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

You can also exclude a predefined filter with `$fetcher->withoutFilter($filterClass)`.

> We recommend adding filters only to specific relations (like we did with "recentArticles").
