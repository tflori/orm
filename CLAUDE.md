## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).

## tflori/orm — usage guide for AI assistants

This is a lightweight ORM for PHP. The sections below cover the patterns you
need to use it correctly. Read them before generating ORM-related code.

### EntityManager setup

```php
$em = new ORM\EntityManager([
    ORM\EntityManager::OPT_CONNECTION => new PDO('sqlite::memory:'),
]);
```

The connection can be a PDO instance, a `DbConfig`, a callable returning PDO, or
an array of constructor args for `DbConfig`. One EntityManager per connection.
`EntityManager::getInstance()` returns the last created instance — useful inside
Entity methods.

### Entity definition

```php
class Article extends ORM\Entity
{
    // Table name derived from class name by default: "articles"
    // Override with: protected static $tableName = 'blog_articles';
    // Or with a template: protected static $tableNameTemplate = '%short%s';
}
```

Column names map to camelCase attributes by default (`created_at` → `$createdAt`).
Primary key defaults to `id`. For composite or custom PKs:

```php
protected static $primaryKeyVars = ['user_id', 'role_id'];
```

### Persisting entities

```php
// Create
$article = new Article(['title' => 'Hello']);
$article->save();   // calls $em->insert() internally

// Update
$article->title = 'World';
$article->save();   // calls $em->update() if the entity already exists

// Delete
$article->delete();
```

**Never call `$em->insert()` / `$em->update()` / `$em->delete()` directly from
application code.** Those are internal methods called by `Entity::save()` and
`Entity::delete()`. Call `save()` and `delete()` on the entity instead.

### Fetching entities

```php
// By primary key — returns entity or null
$article = $em->fetch(Article::class, 42);

// Query — returns EntityFetcher (extends QueryBuilder)
$articles = $em->fetch(Article::class)
    ->where('published', true)
    ->orderBy('created_at', 'DESC')
    ->all();

// Single result
$first = $em->fetch(Article::class)->where('slug', $slug)->one();

// Count
$count = $em->fetch(Article::class)->where('published', true)->count();
```

### Relations

Relations are defined in `protected static $relations`. The array structure determines
the type — there is no explicit type key except for OneToOne.

```php
// OneToMany  — FK 'articleId' is on the Comment side
'comments' => [Comment::class, 'article']

// Owner (belongs-to)  — FK 'userId' is on THIS entity
'author' => [User::class, ['userId' => 'id']]

// OneToOne  — non-owner side needs 'one' as first element
'additionalData' => ['one', ArticleAdditionalData::class, 'article']

// ManyToMany  — pivot table 'article_category', opposite relation 'articles'
'categories' => [Category::class, ['id' => 'article_id'], 'articles', 'article_category']
// Opposite relation
'articles'  => [Article::class, ['article_id' => 'id'], 'categories', 'article_category']

// Morphed  — 'parentType' map determines the actual class
'parent' => [['parentType' => ['article' => Article::class, 'image' => Image::class]], ['parentId' => 'id']]

// ParentChildren  — self-referential, detected automatically when class references itself
'parent'   => [self::class, ['parentId' => 'id']]
'children' => [self::class, 'parent']
```

Since 1.9: alternatively define a `<name>Relation()` static method or assign in `boot()`.
All styles are equivalent.

Access related entities:

```php
$comments = $article->getRelated('comments');   // fetches if not loaded
$em->eagerLoad('comments', ...$articles);       // avoids N+1
```

### Events and observers

```php
$em->observe(Article::class)
    ->on('inserted', function (Article $article) { /* ... */ })
    ->on('deleted', function (Article $article) { /* ... */ });
```

Built-in events: `fetched`, `inserted`, `updated`, `deleted`, `inserting`,
`updating`, `deleting`. Return `false` from any pre-event handler (`inserting`,
`updating`, `deleting`) to cancel the operation.

For reusable observers extend `ORM\Observer\AbstractObserver`:

```php
class AuditLog extends ORM\Observer\AbstractObserver
{
    public function inserted(ORM\Event\Inserted $event) { /* ... */ }
    public function deleted(ORM\Event\Deleted $event)   { /* ... */ }
}
$em->observe(Article::class, new AuditLog());
```

### Bulk inserts

```php
$em->useBulkInserts(Article::class, limit: 100);
foreach ($rows as $row) {
    (new Article($row))->save();   // batched automatically
}
$em->finishBulkInserts(Article::class);
```

### Testing

Use the `ORM\Testing\MocksEntityManager` trait in your test case:

```php
class MyTest extends TestCase
{
    use ORM\Testing\MocksEntityManager;

    protected function setUp(): void
    {
        $this->initMock();   // sets up $this->em as a mock EntityManager
    }
}
```

Then use `$this->em->shouldReceive(...)` (Mockery) or the built-in
`ormAddResult` / `ormExpectInsert` / `ormExpectUpdate` / `ormExpectDelete`
helpers to set expectations without a real database.

### Common mistakes

- Do not call `$em->insert()` directly — call `$entity->save()`.
- Do not instantiate EntityFetcher manually — use `$em->fetch(Class::class)`.
- Relations are defined via `protected static $relations` (array), `<name>Relation()` static
  method, or `boot()` — never as plain instance methods.
- `$em->fetch(Class::class, $id)` returns `null` when not found, not an exception.
- `EntityFetcher::one()` returns `null` on no result; `EntityFetcher::oneOrFail()`
  throws.