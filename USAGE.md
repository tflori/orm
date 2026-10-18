# tflori/orm — usage guide

A short guide to use tflori/orm correctly - written for developers and AI assistants. If you use an AI assistant in
your project, point it to this file (e.g. in your `AGENTS.md` or `CLAUDE.md`: "For tflori/orm read
`vendor/tflori/orm/USAGE.md`"). The full documentation is at https://tflori.github.io/orm.

## EntityManager setup

```php
$em = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTION => new PDO('sqlite::memory:'),
]);
```

The connection can be a PDO instance, a `DbConfig`, a callable returning PDO, or an array of constructor args for
`DbConfig`. One EntityManager per connection. `EntityManager::getInstance()` returns the last created instance.

## Entity definition

```php
class Article extends ORM\Entity
{
    // table name derived from the short class name by default: "article"
    // override with: protected static $tableName = 'blog_articles';
    // or with a template: protected static $tableNameTemplate = '%short%s';
}
```

Column names map to camelCase attributes by default (`created_at` → `$createdAt`). The primary key defaults to `id`.
For composite or custom primary keys:

```php
protected static $primaryKey = ['userId', 'roleId'];
protected static $autoIncrement = false;
```

## Persisting entities

```php
$article = new Article(['title' => 'Hello']);
$article->save();          // insert

$article->title = 'World';
$article->save();          // update

$article->delete();
```

Never call `$em->insert()` / `$em->update()` / `$em->delete()` from application code - they are internal methods used
by `Entity::save()` and `Entity::delete()`.

## Fetching entities

```php
$article = $em->fetch(Article::class, 42);           // by primary key - entity or null

$articles = $em->fetch(Article::class)               // EntityFetcher (extends QueryBuilder)
    ->where('published', true)
    ->orderBy('created_at', 'DESC')
    ->all();

$first = $em->fetch(Article::class)->where('slug', $slug)->one();  // entity or null
$count = $em->fetch(Article::class)->where('published', true)->count();
```

Do not instantiate `EntityFetcher` manually - use `$em->fetch(Class::class)` or `Article::query()`.

## Relations

Relations are defined in `protected static $relations`. The array structure determines the type.

```php
// one-to-many - the foreign key 'articleId' is on the Comment side
'comments' => [Comment::class, 'article'],

// owner (belongs-to) - the foreign key 'userId' is on this entity
'author' => [User::class, ['userId' => 'id']],

// one-to-one - the non-owner side needs 'one' as first element
'additionalData' => ['one', ArticleAdditionalData::class, 'article'],

// many-to-many - association table 'article_category', opposite relation 'articles'
'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category'],
// opposite relation in Category
'articles' => [Article::class, ['id' => 'category_id'], 'categories', 'article_category'],

// morphed - the column 'parentType' defines the related class
'parent' => [['parentType' => ['article' => Article::class, 'image' => Image::class]], ['parentId' => 'id']],

// parent-children - self-referential, detected automatically
'parent'   => [self::class, ['parentId' => 'id']],
'children' => [self::class, 'parent'],
```

Alternatively define a static `<name>Relation()` method or use `static::defineRelation($name, $definition)` in
`boot()`. Prefer the static property. Never assign to `static::$relations` in `boot()`, and always call
`parent::boot()` in `boot()`. Relations are never plain instance methods.

```php
$user     = $article->author;                         // magic getter = $article->getRelated('author')
$comments = $article->fetch('comments')->where('approved', true)->all();
$article->setRelated('author', $user);                // owner only - save() afterwards
$article->addRelated('categories', [$category]);      // many-to-many only
$articles = $em->fetch(Article::class)->with('author', 'comments')->all();  // eager load (avoids N+1)
$em->eagerLoad('comments', ...$articles);
```

## Events and observers

```php
$em->observe(Article::class)
    ->on('inserted', function (ORM\Event\Inserted $event) { /* $event->entity */ })
    ->on('deleting', function (ORM\Event\Deleting $event) { return false; /* cancels the delete */ });
```

Events: `fetched`, `changed`, `saving`, `saved`, `inserting`, `inserted`, `updating`, `updated`, `deleting` and
`deleted`. Returning `false` from a handler of `saving`, `inserting`, `updating` or `deleting` cancels the operation.
For
reusable observers extend `ORM\Observer\AbstractObserver` and register it with `$em->observe(Article::class, $observer)`.

## Bulk inserts

```php
$em->useBulkInserts(Article::class, 100);
foreach ($rows as $row) {
    (new Article($row))->save();   // batched automatically
}
$em->finishBulkInserts(Article::class);
```

## Testing

Use the `ORM\Testing\MocksEntityManager` trait (requires `mockery/mockery` `^1.6`):

```php
class ArticleTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    use ORM\Testing\MocksEntityManager;

    protected $em;

    protected function setUp(): void
    {
        $this->em = $this->ormInitMock();
    }

    public function testArchivesArticles()
    {
        $this->ormAddResult(Article::class, new Article(['id' => 1]))
            ->where('id', [1])
            ->expectUpdate(['status' => 'archived']);   // expects exactly one call by default

        archiveArticles([1]);
    }
}
```

Helpers: `ormCreateMockedEntity()`, `ormAddResult()` (with `where()`, `expectOne()`, `expectAll()`,
`expectCount()`, `expectDelete()`, `expectUpdate()`, `expectInsert()`), `ormExpectInsert()`, `ormExpectUpdate()`,
`ormExpectDelete()`. Don't use the deprecated `ormExpectFetch()`.
